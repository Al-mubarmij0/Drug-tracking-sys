<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';
$page_title = "Pharmacist Dashboard";

// Total stock items (sum of all quantities)
$totalStock = 0;
$stockRes = $conn->query("SELECT SUM(quantity) AS total FROM stock");
if ($stockRes) {
    $row = $stockRes->fetch_assoc();
    $totalStock = $row['total'] ?? 0;
}

// Expiring in 30 days
$expiringSoon = 0;
$expQuery = "SELECT COUNT(*) AS total FROM stock WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
$expRes = $conn->query($expQuery);
if ($expRes) {
    $expiringSoon = $expRes->fetch_assoc()['total'] ?? 0;
}

// Total distributions
$totalDistributions = 0;
$distRes = $conn->query("SELECT SUM(quantity) AS total FROM distributions");
if ($distRes) {
    $totalDistributions = $distRes->fetch_assoc()['total'] ?? 0;
}

// Low stock items (quantity less than or equal to 10)
$lowStockItems = [];
$lowStockRes = $conn->query("SELECT drug_name, quantity FROM stock WHERE quantity <= 10 ORDER BY quantity ASC LIMIT 10");
if ($lowStockRes) {
    while ($row = $lowStockRes->fetch_assoc()) {
        $lowStockItems[] = $row;
    }
}
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<?php include '../partials/navbar.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Welcome, Pharmacist</h3>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-lightning-charge"></i> Quick Actions
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="../stock/add_stock.php"><i class="bi bi-capsule-plus me-2"></i> Add Stock</a></li>
                <li><a class="dropdown-item" href="../distributions/distribute.php"><i class="bi bi-send-check me-2"></i> Distribute Drug</a></li>
                <li><a class="dropdown-item" href="../reports/stock_report.php"><i class="bi bi-bar-chart me-2"></i> View Reports</a></li>
            </ul>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-info text-center">
                <div class="card-body">
                    <i class="bi bi-capsule display-6"></i>
                    <h5 class="card-title mt-2">Total Stock Items</h5>
                    <p class="card-text fs-4"><?= $totalStock ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-warning text-center">
                <div class="card-body">
                    <i class="bi bi-alarm display-6"></i>
                    <h5 class="card-title mt-2">Expiring in 30 Days</h5>
                    <p class="card-text fs-4"><?= $expiringSoon ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-success text-center">
                <div class="card-body">
                    <i class="bi bi-send display-6"></i>
                    <h5 class="card-title mt-2">Total Distributions</h5>
                    <p class="card-text fs-4"><?= $totalDistributions ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong><i class="bi bi-exclamation-triangle me-2 text-danger"></i> Low Stock Alerts</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Drug Name</th>
                        <th>Quantity Left</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($lowStockItems)): ?>
                        <?php foreach ($lowStockItems as $drug): ?>
                            <tr>
                                <td><?= htmlspecialchars($drug['drug_name']) ?></td>
                                <td class="text-danger fw-bold"><?= $drug['quantity'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="2" class="text-center">No low stock alerts.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <strong><i class="bi bi-graph-up-arrow me-2"></i> Monthly Distributions</strong>
        </div>
        <div class="card-body">
            <canvas id="distributionChart" height="80"></canvas>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('distributionChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Drugs Distributed',
                data: [20, 25, 18, 30, 22, 27], // You can fetch real monthly data here
                backgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
