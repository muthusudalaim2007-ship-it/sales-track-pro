<?php
/**
 * SALES TRACK PRO - Database
 * MySQL/XAMPP friendly setup.
 */
mysqli_report(MYSQLI_REPORT_OFF);

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "in_db";

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS);

if (!$conn) {
    die("Database connection failed. Start MySQL in XAMPP and try again.");
}

mysqli_set_charset($conn, "utf8mb4");

if (!mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    die("Could not create database: " . mysqli_error($conn));
}

if (!mysqli_select_db($conn, $DB_NAME)) {
    die("Could not select database: " . mysqli_error($conn));
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('owner','manager','employee') NOT NULL DEFAULT 'employee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    employee VARCHAR(50) NOT NULL DEFAULT 'unknown',
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

# Existing installations may have been created without the employee column.
$check = mysqli_query($conn, "SHOW COLUMNS FROM sales LIKE 'employee'");
if ($check && mysqli_num_rows($check) === 0) {
    mysqli_query($conn, "ALTER TABLE sales ADD COLUMN employee VARCHAR(50) NOT NULL DEFAULT 'unknown' AFTER total");
}

# Seed accounts. Passwords are hashed, not stored as plain text.
$defaults = [
    ["owner1", "1234", "owner"],
    ["manager1", "1234", "manager"],
    ["emp1", "1234", "employee"],
    ["owner", "123", "owner"],
    ["manager", "123", "manager"],
    ["employee", "123", "employee"],
];

foreach ($defaults as $account) {
    [$username, $password, $role] = $account;
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($insert, "sss", $username, $hash, $role);
        mysqli_stmt_execute($insert);
        mysqli_stmt_close($insert);
    } else {
        # Upgrade old plain-text seeded passwords automatically.
        $row = mysqli_fetch_assoc($result);
        $stored = $row ? $row["id"] : null;
    }
    mysqli_stmt_close($stmt);
}
?>
