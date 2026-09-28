<?php
require_once "auth.php";
require_roles(["employee"]);
header("Location: dashboard.php");
exit;
?>