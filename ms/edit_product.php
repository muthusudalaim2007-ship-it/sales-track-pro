<?php
require_once "db.php";
require_once "auth.php";
require_once "layout.php";
require_roles(["employee","owner"]);

$id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);
if (!$id) { header("Location: view_items.php"); exit; }

$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$product) { flash("error","Product not found."); header("Location: view_items.php"); exit; }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $qty = filter_var($_POST["qty"] ?? null, FILTER_VALIDATE_INT);
    $price = filter_var($_POST["price"] ?? null, FILTER_VALIDATE_FLOAT);

    if ($name === "" || $qty === false || $qty < 0 || $price === false || $price < 0) {
        flash("error","Please enter valid values.");
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE products SET name=?, quantity=?, price=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sidi", $name, $qty, $price, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        flash("success","Product updated successfully.");
        header("Location: view_items.php");
        exit;
    }
}
page_start("Edit Product", "inventory");
?>
<section class="panel" style="max-width:720px">
<div class="panel-head"><div><h2>Edit product</h2><p class="note">Update stock, name or pricing.</p></div></div>
<form method="post"><div class="form-grid">
<div class="field full"><label>Item name</label><input name="name" value="<?= e($product["name"]) ?>" required></div>
<div class="field"><label>Quantity</label><input type="number" name="qty" min="0" value="<?= (int)$product["quantity"] ?>" required></div>
<div class="field"><label>Unit price (₹)</label><input type="number" name="price" min="0" step="0.01" value="<?= e($product["price"]) ?>" required></div>
</div><div class="form-actions"><button class="btn btn-primary">Save changes</button><a class="btn btn-secondary" href="view_items.php">Cancel</a></div></form>
</section>
<?php page_end(); ?>
