<?php
require_once "db.php";
require_once "auth.php";
require_once "layout.php";
require_roles(["manager","owner"]);

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) t, COUNT(*) orders FROM sales"));
$monthly = mysqli_query($conn, "SELECT DATE(sale_date) d, SUM(total) t, SUM(quantity) units FROM sales GROUP BY DATE(sale_date) ORDER BY d DESC LIMIT 14");
$top = mysqli_query($conn, "SELECT item_name,SUM(quantity) q,SUM(total) revenue FROM sales GROUP BY item_name ORDER BY revenue DESC LIMIT 5");
page_start("Sales Analysis", "sales-analysis");
?>
<div class="grid stats">
<div class="stat-card"><div class="stat-top"><span>Total revenue</span><span class="stat-icon">₹</span></div><div class="stat-value"><?= money($total["t"]) ?></div><div class="stat-note">All recorded sales</div></div>
<div class="stat-card"><div class="stat-top"><span>Orders</span><span class="stat-icon">↗</span></div><div class="stat-value"><?= (int)$total["orders"] ?></div><div class="stat-note">Transactions recorded</div></div>
</div>
<div class="grid two-col">
<section class="panel"><div class="panel-head"><h2>Daily sales</h2></div><div class="table-wrap"><table><thead><tr><th>Date</th><th>Units</th><th>Revenue</th></tr></thead><tbody>
<?php if(mysqli_num_rows($monthly)===0): ?><tr><td colspan="3"><div class="empty">No sales data yet.</div></td></tr>
<?php else: while($r=mysqli_fetch_assoc($monthly)): ?><tr><td><?= e(date("d M Y",strtotime($r["d"]))) ?></td><td><?= (int)$r["units"] ?></td><td><?= money($r["t"]) ?></td></tr><?php endwhile; endif; ?>
</tbody></table></div></section>
<section class="panel"><div class="panel-head"><h2>Top products by revenue</h2></div>
<?php if(mysqli_num_rows($top)===0): ?><div class="empty">No sales data yet.</div><?php else: while($r=mysqli_fetch_assoc($top)): ?><div style="display:flex;justify-content:space-between;gap:10px;padding:12px 0;border-bottom:1px solid var(--line)"><div><strong><?= e($r["item_name"]) ?></strong><div class="note"><?= (int)$r["q"] ?> units sold</div></div><strong><?= money($r["revenue"]) ?></strong></div><?php endwhile; endif; ?>
</section></div>
<?php page_end(); ?>
