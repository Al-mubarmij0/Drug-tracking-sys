<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/drug-tracking-sys/";
$current_page = basename($_SERVER['SCRIPT_NAME']);
$role = $_SESSION['role'] ?? 'guest';
$username = $_SESSION['user_name'] ?? $role;
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $base_url ?>dashboard.php">
            Drug Tracking System
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'list_users.php' ? 'active' : '' ?>" href="<?= $base_url ?>users/list_users.php">
                            <i class="bi bi-people-fill me-1"></i> Manage Users
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'procurement'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'list_suppliers.php' ? 'active' : '' ?>" href="<?= $base_url ?>suppliers/list_suppliers.php">
                            <i class="bi bi-truck me-1"></i> Suppliers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'list_procurements.php' ? 'active' : '' ?>" href="<?= $base_url ?>procurements/list_procurements.php">
                            <i class="bi bi-box-seam me-1"></i> Procurements
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'pharmacist'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'list_stock.php' ? 'active' : '' ?>" href="<?= $base_url ?>stock/list_stock.php">
                            <i class="bi bi-capsule me-1"></i> Stock
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'list_distributions.php' ? 'active' : '' ?>" href="<?= $base_url ?>distributions/list_distributions.php">
                            <i class="bi bi-send me-1"></i> Distributions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'stock_report.php' ? 'active' : '' ?>" href="<?= $base_url ?>reports/stock_report.php">
                            <i class="bi bi-bar-chart-line me-1"></i> Reports
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Search -->
            <form class="d-flex me-3" method="GET" action="<?= $base_url ?>search.php">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search..." aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <!-- Profile Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= $base_url ?>assets/img/user.png" alt="User" width="32" height="32" class="rounded-circle me-2">
                    <strong><?= htmlspecialchars($username) ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow text-small">
                    <li><a class="dropdown-item" href="<?= $base_url ?>profile.php"><i class="bi bi-person me-2"></i> Profile</a></li>
                    <li><a class="dropdown-item" href="<?= $base_url ?>settings.php"><i class="bi bi-gear me-2"></i> Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="#" onclick="confirmLogout(event)">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Logout Confirmation -->
<script>
function confirmLogout(event) {
    event.preventDefault();
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "<?= $base_url ?>logout.php";
    }
}
</script>
