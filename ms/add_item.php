<?php
require_once "db.php";
require_once "auth.php";
require_once "layout.php";
require_roles(["employee","owner"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $qty = filter_var($_POST["qty"] ?? null, FILTER_VALIDATE_INT);
    $price = filter_var($_POST["price"] ?? null, FILTER_VALIDATE_FLOAT);

    if ($name === "" || $qty === false || $qty < 0 || $price === false || $price < 0) {
        flash("error", "Enter a valid item name, quantity and price.");
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM products WHERE name=? LIMIT 1");
        mysqli_stmt_bind_param($check, "s", $name);
        mysqli_stmt_execute($check);
        $existing = mysqli_stmt_get_result($check);
        mysqli_stmt_close($check);

        if (mysqli_num_rows($existing) > 0) {
            $row = mysqli_fetch_assoc($existing);
            $stmt = mysqli_prepare($conn, "UPDATE products SET quantity=quantity+?, price=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "idi", $qty, $price, $row["id"]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            flash("success", "Existing item updated with the new stock.");
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO products(name,quantity,price) VALUES(?,?,?)");
            mysqli_stmt_bind_param($stmt, "sid", $name, $qty, $price);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            flash("success", "Item added successfully.");
        }
        header("Location: view_items.php");
        exit;
    }
}
page_start("Add Item", "add");
?>
<section class="panel" style="max-width:720px">
<div class="panel-head"><div><h2>Add inventory</h2><p class="note">Add a new product or add stock to an existing product.</p></div></div>
<form method="post">
<div class="form-grid">
<div class="field full"><label>Item name</label><input name="name" maxlength="100" required placeholder="e.g. Wireless Headphones"></div>
<div class="field"><label>Quantity</label><input type="number" name="qty" min="0" step="1" required placeholder="0"></div>
<div class="field"><label>Unit price (₹)</label><input type="number" name="price" min="0" step="0.01" required placeholder="0.00"></div>
</div>
<div class="form-actions"><button class="btn btn-primary">Save item</button><a class="btn btn-secondary" href="view_items.php">Cancel</a></div>
</form></section>
<?php page_end(); ?>
