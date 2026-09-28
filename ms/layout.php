<?php
require_once __DIR__ . "/auth.php";

function page_start(string $title, string $active = ""): void {
    $flash = get_flash();
    $role = current_role();
    $user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> · Sales Track Pro</title>
<link rel="stylesheet" href="app.css">
</head>
<body>
<div class="app-shell">
<aside class="sidebar" id="sidebar">
    <a class="brand" href="dashboard.php">
        <span class="brand-mark">ST</span>
        <span><strong>Sales Track</strong><small>PRO</small></span>
    </a>

    <nav class="nav">
        <a class="<?= $active === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">⌂ <span>Dashboard</span></a>

        <?php if (in_array($role, ['employee','owner'], true)): ?>
            <a class="<?= $active === 'add' ? 'active' : '' ?>" href="add_item.php">＋ <span>Add Item</span></a>
            <a class="<?= $active === 'inventory' ? 'active' : '' ?>" href="view_items.php">▦ <span>Inventory</span></a>
            <a class="<?= $active === 'sales' ? 'active' : '' ?>" href="sale_item.php">↗ <span>Record Sale</span></a>
        <?php endif; ?>

        <?php if (in_array($role, ['manager','owner'], true)): ?>
            <div class="nav-label">Analytics</div>
            <a class="<?= $active === 'employees' ? 'active' : '' ?>" href="manager_employee.php">♙ <span>Employees</span></a>
            <a class="<?= $active === 'inventory-analysis' ? 'active' : '' ?>" href="manager_inventory_analysis.php">◫ <span>Inventory Analysis</span></a>
            <a class="<?= $active === 'sales-analysis' ? 'active' : '' ?>" href="manager_sales_analysis.php">◔ <span>Sales Analysis</span></a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-bottom">
        <div class="user-mini">
            <div class="avatar"><?= strtoupper(substr($user, 0, 1)) ?></div>
            <div><strong><?= e($user) ?></strong><small><?= ucfirst(e($role)) ?></small></div>
        </div>
        <a class="logout-link" href="logout.php">↪ Logout</a>
    </div>
</aside>

<main class="main-content">
    <header class="topbar">
        <button class="mobile-menu" onclick="toggleSidebar()" aria-label="Open menu">☰</button>
        <div>
            <p class="eyebrow">Inventory & sales</p>
            <h1><?= e($title) ?></h1>
        </div>
        <div class="topbar-user">
            <span class="status-dot"></span>
            <?= e($user) ?>
        </div>
    </header>

    <?php if ($flash): ?>
        <div class="flash <?= e($flash['type']) ?>" data-autohide>
            <span><?= $flash['type'] === 'success' ? '✓' : '!' ?></span>
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>
<?php
}

function page_end(): void {
?>
</main>
</div>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}
setTimeout(() => document.querySelectorAll('[data-autohide]').forEach(el => {
    el.classList.add('hide');
    setTimeout(() => el.remove(), 400);
}), 4200);

document.querySelectorAll('.confirm-delete').forEach(link => {
    link.addEventListener('click', e => {
        if (!confirm('Delete this product? Existing sales records will remain.')) e.preventDefault();
    });
});
</script>
</body>
</html>
<?php
}
?>
