<?php
require_once "db.php";
require_once "auth.php";
require_once "layout.php";
require_roles(["manager","owner"]);
$low = mysqli_query($conn, "SELECT * FROM products WHERE quantity < 5 ORDER BY quantity ASC, name");
$top = mysqli_query($conn, "SELECT item_name, SUM(quantity) q FROM sales GROUP BY item_name ORDER BY q DESC LIMIT 5");
$totalStock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(quantity),0) total FROM products"))["total"];
$pageProducts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM products"))["c"];
page_start("Inventory Analysis", "inventory-analysis");
?>
<div class="grid stats">
<div class="stat-card"><div class="stat-top"><span>Total units</span><span class="stat-icon">◫</span></div><div class="stat-value"><?= (int)$totalStock ?></div></div>
<div class="stat-card"><div class="stat-top"><span>Products</span><span class="stat-icon">▦</span></div><div class="stat-value"><?= (int)$pageProducts ?></div></div>
<div class="stat-card"><div class="stat-top"><span>Low stock</span><span class="stat-icon">!</span></div><div class="stat-value"><?= mysqli_num_rows($low) ?></div></div>
</div>
<div class="grid two-col">
<section class="panel"><div class="panel-head"><h2>Low stock alert</h2></div>
<?php if(mysqli_num_rows($low)===0): ?><div class="empty">All products currently have 5+ units.</div><?php else: ?><div style="display:grid;gap:10px"><?php while($r=mysqli_fetch_assoc($low)): ?><div style="display:flex;justify-content:space-between;padding:12px;background:#fff7ed;border-radius:10px"><strong><?= e($r["name"]) ?></strong><span class="badge <?= $r["quantity"]===0?'red':'yellow' ?>"><?= (int)$r["quantity"] ?> left</span></div><?php endwhile; ?></div><?php endif; ?>
</section>
<section class="panel"><div class="panel-head"><h2>Fast-moving items</h2></div>
<?php if(mysqli_num_rows($top)===0): ?><div class="empty">No sales data yet.</div><?php else: $rank=1; while($r=mysqli_fetch_assoc($top)): ?><div style="display:flex;align-items:center;gap:12px;margin-bottom:13px"><span class="badge blue">#<?= $rank++ ?></span><strong style="flex:1"><?= e($r["item_name"]) ?></strong><span><?= (int)$r["q"] ?> sold</span></div><?php endwhile; endif; ?>
</section>
</div>
<?php page_end(); ?>
