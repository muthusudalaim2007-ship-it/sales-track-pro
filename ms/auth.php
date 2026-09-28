<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function require_login(): void {
    if (empty($_SESSION["user"]) || empty($_SESSION["role"])) {
        header("Location: login.php");
        exit;
    }
}

function require_roles(array $roles): void {
    require_login();
    if (!in_array($_SESSION["role"], $roles, true)) {
        http_response_code(403);
        die("Access denied.");
    }
}

function current_user(): string {
    return $_SESSION["user"] ?? "User";
}

function current_role(): string {
    return $_SESSION["role"] ?? "guest";
}

function flash(string $type, string $message): void {
    $_SESSION["flash"] = ["type" => $type, "message" => $message];
}

function get_flash(): ?array {
    $flash = $_SESSION["flash"] ?? null;
    unset($_SESSION["flash"]);
    return $flash;
}

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function money($value): string {
    return "₹" . number_format((float)$value, 2);
}
?>
