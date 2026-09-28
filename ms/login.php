<?php
session_start();
require_once "db.php";
require_once "auth.php";

if (!empty($_SESSION["role"])) {
    header("Location: dashboard.php");
    exit;
}
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = mysqli_prepare($conn, "SELECT username,password,role FROM users WHERE username=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    $valid = false;
    if ($row) {
        $valid = password_verify($password, $row["password"]);
        # Compatibility with older installations that stored "123" in plain text.
        if (!$valid && hash_equals((string)$row["password"], (string)$password)) {
            $valid = true;
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $up = mysqli_prepare($conn, "UPDATE users SET password=? WHERE username=?");
            mysqli_stmt_bind_param($up, "ss", $newHash, $username);
            mysqli_stmt_execute($up);
            mysqli_stmt_close($up);
        }
    }
    mysqli_stmt_close($stmt);

    if ($valid) {
        session_regenerate_id(true);
        $_SESSION["user"] = $row["username"];
        $_SESSION["role"] = $row["role"];
        header("Location: dashboard.php");
        exit;
    }
    $error = "Invalid username or password.";
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login · Sales Track Pro</title><link rel="stylesheet" href="app.css"></head>
<body>
<div class="hero-page">
<div class="auth-card">
  <a class="auth-logo" href="index.php"><span class="brand-mark">ST</span><strong>Sales Track Pro</strong></a>
  <h1>Welcome back</h1><p class="sub">Sign in to manage inventory and sales.</p>
  <?php if ($error): ?><div class="flash error"><span>!</span><?= e($error) ?></div><?php endif; ?>
  <form method="post" autocomplete="on">
    <div class="field"><label>Username</label><input name="username" required autofocus autocomplete="username"></div>
    <div class="field"><label>Password</label><input type="password" name="password" required autocomplete="current-password"></div>
    <button class="btn btn-primary" style="width:100%;padding:13px" name="login">Sign in</button>
  </form>
  <p class="note">Demo accounts: <strong>owner1 / 1234</strong>, <strong>manager1 / 1234</strong>, <strong>emp1 / 1234</strong>.</p>
  <div class="auth-footer">Need an account? <a href="signup.php">Create employee account</a></div>
</div>
</div>
</body></html>
