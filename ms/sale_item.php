<?php
require_once "db.php";
require_once "auth.php";
require_once "layout.php";
require_roles(["employee","owner"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $productId = filter_var($_POST["product_id"] ?? null, FILTER_VALIDATE_INT);
    $qty = filter_var($_POST["qty"] ?? null, FILTER_VALIDATE_INT);

    if (!$productId || $qty === false || $qty < 1) {
        flash("error", "Select a product and enter a quantity of at least 1.");
    } else {
        mysqli_begin_transaction($conn);
        try {
            $stmt = mysqli_prepare($conn, "SELECT name,price,quantity FROM products WHERE id=? FOR UPDATE");
            mysqli_stmt_bind_param($stmt, "i", $productId);
            mysqli_stmt_execute($stmt);
            $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if (!$product) throw new Exception("Product not found.");
            if ((int)$product["quantity"] < $qty) throw new Exception("Not enough stock. Only ".$product["quantity"]." unit(s) available.");

            $total = (float)$product["price"] * $qty;
            $employee = current_user();

            $insert = mysqli_prepare($conn, "INSERT INTO sales(item_name,quantity,price,total,employee) VALUES(?,?,?,?,?)");
            mysqli_stmt_bind_param($insert, "sidds", $product["name"], $qty, $product["price"], $total, $employee);
            mysqli_stmt_execute($insert);
            mysqli_stmt_close($insert);

            $update = mysqli_prepare($conn, "UPDATE products SET quantity=quantity-? WHERE id=?");
            mysqli_stmt_bind_param($update, "ii", $qty, $productId);
            mysqli_stmt_execute($update);
            mysqli_stmt_close($update);

            mysqli_commit($conn);
            flash("success", "Sale recorded successfully.");
            header("Location: sale_item.php");
            exit;
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            flash("error", $e->getMessage());
        }
    }
}

$items = mysqli_query($conn, "SELECT id,name,quantity,price FROM products ORDER BY name");
$recent = mysqli_query($conn, "SELECT * FROM sales WHERE employee='".mysqli_real_escape_string($conn,current_user())."' ORDER BY sale_date DESC LIMIT 5");
page_start("Record Sale", "sales");
?>
<section class="grid two-col">
<div class="panel">
<div class="panel-head"><div><h2>New sale</h2><p class="note">Stock is reduced automatically after a successful sale.</p></div></div>
<form method="post">
<div class="field"><label>Product</label><select name="product_id" required><option value="">Choose product...</option>
<?php while($i=mysqli_fetch_assoc($items)): ?><option value="<?= (int)$i["id"] ?>" <?= $i["quantity"] < 1 ? "disabled" : "" ?>><?= e($i["name"]) ?> · <?= $i["quantity"] ?> in stock · <?= money($i["price"]) ?></option><?php endwhile; ?>
</select></div>
<div class="field" style="margin-top:15px"><label>Quantity</label><input type="number" name="qty" min="1" step="1" required></div>
<div class="form-actions"><button class="btn btn-primary">Record sale</button></div>
</form>
</div>
<div class="panel"><div class="panel-head"><h2>Your recent sales</h2></div>
<?php if(mysqli_num_rows($recent)===0): ?><div class="empty">No sales by you yet.</div><?php else: ?>
<div style="display:grid;gap:11px"><?php while($r=mysqli_fetch_assoc($recent)): ?><div style="display:flex;justify-content:space-between;gap:10px;padding-bottom:11px;border-bottom:1px solid var(--line)"><div><strong style="font-size:13px"><?= e($r["item_name"]) ?></strong><div class="note"><?= (int)$r["quantity"] ?> × <?= money($r["price"]) ?></div></div><strong><?= money($r["total"]) ?></strong></div><?php endwhile; ?></div>
<?php endif; ?></div>
</section>
<?php page_end(); ?>
