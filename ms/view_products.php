<?php
require_once "auth.php";
require_roles(["employee","owner"]);
header("Location: view_items.php");
exit;
?>