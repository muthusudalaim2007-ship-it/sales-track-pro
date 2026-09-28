<?php
require_once "auth.php";
require_roles(["manager","owner"]);
header("Location: dashboard.php");
exit;
?>