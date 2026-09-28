<?php
require_once "db.php";
require_once "auth.php";
require_once "layout.php";
require_login();

$products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) count, COALESCE(SUM(quantity),0) stock FROM products"));
$sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) orders, COALESCE(SUM(total),0) revenue FROM sales"));
$low = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) count FROM products WHERE quantity < 5"));
$recent = mysqli_query($conn, "SELECT * FROM sales ORDER BY sale_date DESC LIMIT 7");

page_start("Dashboard", "dashboard");
?>
<div class="grid stats">
  <div class="stat-card"><div class="stat-top"><span>Total Products</span><span class="stat-icon">▦</span></div><div class="stat-value"><?= (int)$products["count"] ?></div><div class="stat-note">Items in inventory</div></div>
  <div class="stat-card"><div class="stat-top"><span>Units in Stock</span><span class="stat-icon">◫</span></div><div class="stat-value"><?= (int)$products["stock"] ?></div><div class="stat-note">Across all products</div></div>
  <div class="stat-card"><div class="stat-top"><span>Total Orders</span><span class="stat-icon">↗</span></div><div class="stat-value"><?= (int)$sales["orders"] ?></div><div class="stat-note">Recorded sales</div></div>
  <div class="stat-card"><div class="stat-top"><span>Revenue</span><span class="stat-icon">₹</span></div><div class="stat-value"><?= money($sales["revenue"]) ?></div><div class="stat-note"><?= (int)$low["count"] ?> low-stock item(s)</div></div>
</div>

<div class="grid two-col">
<section class="panel">
  <div class="panel-head"><h2>Recent sales</h2><a href="sale_item.php">Record sale →</a></div>
  <div class="table-wrap"><table>
  <thead><tr><th>Item</th><th>Qty</th><th>Employee</th><th>Total</th><th>Date</th></tr></thead>
  <tbody>
  <?php if (mysqli_num_rows($recent) === 0): ?><tr><td colspan="5"><div class="empty">No sales recorded yet.</div></td></tr>
  <?php else: while($r=mysqli_fetch_assoc($recent)): ?>
  <tr><td><strong><?= e($r["item_name"]) ?></strong></td><td><?= (int)$r["quantity"] ?></td><td><?= e($r["employee"]) ?></td><td><?= money($r["total"]) ?></td><td><?= e(date("d M, H:i", strtotime($r["sale_date"]))) ?></td></tr>
  <?php endwhile; endif; ?>
  </tbody></table></div>
</section>
<section class="panel">
  <div class="panel-head"><h2>Quick actions</h2></div>
  <div style="display:grid;gap:10px">
    <?php if (in_array(current_role(),["employee","owner"],true)): ?>
    <a class="btn btn-primary" href="add_item.php">＋ Add inventory item</a>
    <a class="btn btn-secondary" href="view_items.php">▦ Manage inventory</a>
    <a class="btn btn-secondary" href="sale_item.php">↗ Record a sale</a>
    <?php endif; ?>
    <?php if (in_array(current_role(),["manager","owner"],true)): ?>
    <a class="btn btn-secondary" href="manager_sales_analysis.php">◔ View sales analytics</a>
    <a class="btn btn-secondary" href="manager_inventory_analysis.php">◫ Check stock analysis</a>
    <?php endif; ?>
  </div>
</section>
</div>
<?php page_end(); ?>
