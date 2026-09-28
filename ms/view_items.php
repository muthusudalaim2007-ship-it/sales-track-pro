<?php
require_once "db.php";
require_once "auth.php";
require_once "layout.php";
require_roles(["employee","owner"]);

if (isset($_GET["delete"])) {
    $id = filter_var($_GET["delete"], FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $deleted = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        flash($deleted ? "success" : "error", $deleted ? "Product deleted." : "Product not found.");
    }
    header("Location: view_items.php");
    exit;
}

$items = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
page_start("Inventory", "inventory");
?>
<section class="panel">
<div class="search-row"><div><h2>Inventory management</h2><p class="note">Search, edit and remove products.</p></div><a class="btn btn-primary" href="add_item.php">＋ Add item</a></div>
<input class="search" id="inventorySearch" placeholder="Search products...">
<div class="table-wrap" style="margin-top:15px"><table id="inventoryTable">
<thead><tr><th>ID</th><th>Product</th><th>Stock</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php if (mysqli_num_rows($items) === 0): ?><tr><td colspan="6"><div class="empty">No products yet. Add your first item.</div></td></tr>
<?php else: while($r=mysqli_fetch_assoc($items)): $q=(int)$r["quantity"]; ?>
<tr>
<td>#<?= (int)$r["id"] ?></td><td><strong><?= e($r["name"]) ?></strong></td><td><?= $q ?></td><td><?= money($r["price"]) ?></td>
<td><?= $q === 0 ? '<span class="badge red">Out of stock</span>' : ($q < 5 ? '<span class="badge yellow">Low stock</span>' : '<span class="badge green">In stock</span>') ?></td>
<td class="actions"><a class="btn btn-secondary" href="edit_product.php?id=<?= (int)$r["id"] ?>">Edit</a><a class="btn btn-danger confirm-delete" href="view_items.php?delete=<?= (int)$r["id"] ?>">Delete</a></td>
</tr>
<?php endwhile; endif; ?>
</tbody></table></div>
</section>
<script>
document.getElementById('inventorySearch').addEventListener('input', function(){
 const q=this.value.toLowerCase();
 document.querySelectorAll('#inventoryTable tbody tr').forEach(r=>r.style.display=r.innerText.toLowerCase().includes(q)?'':'none');
});
</script>
<?php page_end(); ?>
