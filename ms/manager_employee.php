<?php
require_once "db.php";
require_once "auth.php";
require_once "layout.php";
require_roles(["manager","owner"]);
$q = mysqli_query($conn, "SELECT employee, SUM(total) revenue, SUM(quantity) units, COUNT(*) orders FROM sales GROUP BY employee ORDER BY revenue DESC");
page_start("Employee Performance", "employees");
?>
<section class="panel"><div class="panel-head"><div><h2>Employee performance</h2><p class="note">Sales activity grouped by employee.</p></div></div>
<div class="table-wrap"><table><thead><tr><th>Employee</th><th>Orders</th><th>Units sold</th><th>Revenue</th></tr></thead><tbody>
<?php if(mysqli_num_rows($q)===0): ?><tr><td colspan="4"><div class="empty">No sales data yet.</div></td></tr>
<?php else: while($r=mysqli_fetch_assoc($q)): ?><tr><td><strong><?= e($r["employee"]) ?></strong></td><td><?= (int)$r["orders"] ?></td><td><?= (int)$r["units"] ?></td><td><?= money($r["revenue"]) ?></td></tr><?php endwhile; endif; ?>
</tbody></table></div></section>
<?php page_end(); ?>
