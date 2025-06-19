<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session_check.php';
$page_title = "Dashboard";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <!-- Header + Quick Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Welcome Back, <?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['role']) ?>!</h3>

        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dashboardActions" data-bs-toggle="dropdown">
                <i class="bi bi-lightning-charge"></i> Quick Actions
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dashboardActions">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a class="dropdown-item" href="users/add_user.php"><i class="bi bi-person-plus me-2"></i> Add User</a></li>
                    <li><a class="dropdown-item" href="logs/view_logs.php"><i class="bi bi-clock-history me-2"></i> View Logs</a></li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'procurement'): ?>
                    <li><a class="dropdown-item" href="suppliers/add_supplier.php"><i class="bi bi-building-add me-2"></i> Add Supplier</a></li>
                    <li><a class="dropdown-item" href="procurements/add_procurement.php"><i class="bi bi-cart-plus me-2"></i> New Procurement</a></li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'pharmacist'): ?>
                    <li><a class="dropdown-item" href="stock/add_stock.php"><i class="bi bi-plus-circle me-2"></i> Add Stock</a></li>
                    <li><a class="dropdown-item" href="distributions/distribute.php"><i class="bi bi-send-check me-2"></i> Distribute Drug</a></li>
                    <li><a class="dropdown-item" href="reports/stock_report.php"><i class="bi bi-bar-chart me-2"></i> Stock Report</a></li>
                <?php endif; ?>

                <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i> Edit Profile</a></li>
                <li><a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Logout?');"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- Example Dashboard Cards -->
    <div class="row g-4">
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="bi bi-person-circle display-6"></i>
                        <h5 class="card-title mt-2">Total Users</h5>
                        <p class="card-text">...</p> <!-- Replace with user count -->
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'procurement'): ?>
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="bi bi-box display-6"></i>
                        <h5 class="card-title mt-2">Total Procurements</h5>
                        <p class="card-text">...</p> <!-- Replace with dynamic value -->
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'pharmacist'): ?>
            <div class="col-md-4">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="bi bi-capsule display-6"></i>
                        <h5 class="card-title mt-2">Stock Items</h5>
                        <p class="card-text">...</p> <!-- Replace with stock count -->
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
