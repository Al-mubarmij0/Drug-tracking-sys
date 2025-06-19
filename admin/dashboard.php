<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';

$page_title = "Admin Dashboard";

// Total users
$userCount = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM users");
if ($result && $row = $result->fetch_assoc()) {
    $userCount = $row['total'];
}

// Total Logs
$logCount = 0;
$resLogs = $conn->query("SELECT COUNT(*) AS total FROM logs");
if ($resLogs && $row = $resLogs->fetch_assoc()) {
    $logCount = $row['total'];
}

// Last Login
$lastLogin = 'N/A';
$userId = $_SESSION['user_id'];
$resLogin = $conn->query("SELECT timestamp FROM logs WHERE user_id = $userId AND action LIKE '%logged in%' ORDER BY timestamp DESC LIMIT 1");
if ($resLogin && $row = $resLogin->fetch_assoc()) {
    $lastLogin = date("M d, Y h:i A", strtotime($row['timestamp']));
}

// Recent Logs (latest 5 entries)
$recentLogs = [];
$resRecent = $conn->query("SELECT l.action, l.timestamp, u.name FROM logs l JOIN users u ON l.user_id = u.id ORDER BY l.timestamp DESC LIMIT 5");
if ($resRecent) {
    while ($row = $resRecent->fetch_assoc()) {
        $recentLogs[] = $row;
    }
}

// Stock Count
$stockCount = 0;
$resStock = $conn->query("SELECT COUNT(*) AS total FROM stock");
if ($resStock && $row = $resStock->fetch_assoc()) {
    $stockCount = $row['total'];
}

// Supplier Count
$supplierCount = 0;
$resSuppliers = $conn->query("SELECT COUNT(*) AS total FROM suppliers");
if ($resSuppliers && $row = $resSuppliers->fetch_assoc()) {
    $supplierCount = $row['total'];
}

// Procurement Count
$procurementCount = 0;
$resProcurements = $conn->query("SELECT COUNT(*) AS total FROM procurements");
if ($resProcurements && $row = $resProcurements->fetch_assoc()) {
    $procurementCount = $row['total'];
}

// Distributions Count
$distributionCount = 0;
$resDistributions = $conn->query("SELECT COUNT(*) AS total FROM distributions");
if ($resDistributions && $row = $resDistributions->fetch_assoc()) {
    $distributionCount = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<?php include '../partials/navbar.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Welcome Back, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>!</h3>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dashboardActions" data-bs-toggle="dropdown">
                <i class="bi bi-lightning-charge"></i> Quick Actions
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dashboardActions">
                <li><a class="dropdown-item" href="../users/add_user.php"><i class="bi bi-person-plus me-2"></i> Add User</a></li>
                <li><a class="dropdown-item" href="../suppliers/add_supplier.php"><i class="bi bi-building-add me-2"></i> Add Supplier</a></li>
                <li><a class="dropdown-item" href="../procurements/add_procurement.php"><i class="bi bi-cart-plus me-2"></i> New Procurement</a></li>
                <li><a class="dropdown-item" href="../stock/add_stock.php"><i class="bi bi-capsule-plus me-2"></i> Add Stock</a></li>
                <li><a class="dropdown-item" href="../distributions/distribute.php"><i class="bi bi-send-check me-2"></i> Distribute Drug</a></li>
                <li><a class="dropdown-item" href="../reports/stock_report.php"><i class="bi bi-bar-chart me-2"></i> View Reports</a></li>
                <li><a class="dropdown-item" href="../logs/view_logs.php"><i class="bi bi-clock-history me-2"></i> View Logs</a></li>
            </ul>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card text-bg-primary text-center">
                <div class="card-body">
                    <i class="bi bi-person-lines-fill display-6"></i>
                    <h5>Total Users</h5>
                    <p class="fs-4"><?= $userCount ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-secondary text-center">
                <div class="card-body">
                    <i class="bi bi-clock-history display-6"></i>
                    <h5>Total Logs</h5>
                    <p class="fs-4"><?= $logCount ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-success text-center">
                <div class="card-body">
                    <i class="bi bi-person-check display-6"></i>
                    <h5>Last Login</h5>
                    <p class="fs-6"><?= $lastLogin ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-warning text-center">
                <div class="card-body">
                    <i class="bi bi-terminal-split display-6"></i>
                    <h5>Recent Logs</h5>
                    <p class="fs-6">See last 5 entries below</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-info text-center">
                <div class="card-body">
                    <i class="bi bi-boxes display-6"></i>
                    <h5>Total Stock Items</h5>
                    <p class="fs-4"><?= $stockCount ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-dark text-center">
                <div class="card-body">
                    <i class="bi bi-truck display-6"></i>
                    <h5>Total Suppliers</h5>
                    <p class="fs-4"><?= $supplierCount ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-danger text-center">
                <div class="card-body">
                    <i class="bi bi-cart-check display-6"></i>
                    <h5>Total Procurements</h5>
                    <p class="fs-4"><?= $procurementCount ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-light text-center">
                <div class="card-body">
                    <i class="bi bi-send display-6"></i>
                    <h5>Total Distributions</h5>
                    <p class="fs-4"><?= $distributionCount ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logs Table -->
    <div class="mt-4">
        <h5>Recent Activity Logs</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Action</th>
                    <th>User</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentLogs as $index => $log): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['name']) ?></td>
                        <td><?= date("Y-m-d H:i", strtotime($log['timestamp'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>