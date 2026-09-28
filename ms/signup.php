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
    $confirm = $_POST["confirm_password"] ?? "";

    if (strlen($username) < 3) {
        $error = "Username must contain at least 3 characters.";
    } elseif (strlen($password) < 4) {
        $error = "Password must contain at least 4 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE username=?");
        mysqli_stmt_bind_param($check, "s", $username);
        mysqli_stmt_execute($check);
        $exists = mysqli_stmt_get_result($check);
        mysqli_stmt_close($check);

        if (mysqli_num_rows($exists) > 0) {
            $error = "That username already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = "employee";
            $stmt = mysqli_prepare($conn, "INSERT INTO users(username,password,role) VALUES(?,?,?)");
            mysqli_stmt_bind_param($stmt, "sss", $username, $hash, $role);
            if (mysqli_stmt_execute($stmt)) {
                flash("success", "Account created. You can now sign in.");
                header("Location: login.php");
                exit;
            }
            $error = "Could not create the account.";
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sign up · Sales Track Pro</title><link rel="stylesheet" href="app.css"></head>
<body><div class="hero-page"><div class="auth-card">
<a class="auth-logo" href="index.php"><span class="brand-mark">ST</span><strong>Sales Track Pro</strong></a>
<h1>Create account</h1><p class="sub">New accounts are created as employees.</p>
<?php if ($error): ?><div class="flash error"><span>!</span><?= e($error) ?></div><?php endif; ?>
<form method="post">
<div class="field"><label>Username</label><input name="username" minlength="3" required></div>
<div class="field"><label>Password</label><input type="password" name="password" minlength="4" required></div>
<div class="field"><label>Confirm password</label><input type="password" name="confirm_password" minlength="4" required></div>
<button class="btn btn-primary" style="width:100%;padding:13px">Create account</button>
</form>
<div class="auth-footer"><a href="login.php">← Back to login</a></div>
</div></div></body></html>
