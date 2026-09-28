<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!empty($_SESSION["role"])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sales Track Pro</title><link rel="stylesheet" href="app.css">
</head>
<body>
<div class="landing">
  <div class="landing-card">
    <div class="landing-mark">ST</div>
    <h1>SALES TRACK PRO</h1>
    <p>Simplify stock. Amplify sales.</p>
    <a href="login.php" class="btn btn-primary">Open Dashboard →</a>
  </div>
</div>
</body>
</html>
