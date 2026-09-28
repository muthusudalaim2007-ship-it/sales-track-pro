<?php
require_once "db.php";
require_once "auth.php";
require_roles(["employee","owner"]);
$id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);
if ($id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    flash("success", "Product deleted.");
}
header("Location: view_items.php");
exit;
?>