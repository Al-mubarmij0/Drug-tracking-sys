<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';

$page_title = "Procurement Dashboard";

// Fetch total suppliers
$supplierRes = $conn->query("SELECT COUNT(*) AS total FROM suppliers");
$totalSuppliers = $supplierRes ? $supplierRes->fetch_assoc()['total'] : 0;

// Fetch total procurements
$procurementRes = $conn->query("SELECT COUNT(*) AS total FROM procurements");
$totalProcurements = $procurementRes ? $procurementRes->fetch_assoc()['total'] : 0;

// Fetch recent procurements
$recentProcurements = [];
$recentQuery = "SELECT p.reference_no AS ref, s.name AS supplier, p.date_procured AS date
                FROM procurements p
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                ORDER BY p.date_procured DESC
                LIMIT 5";
$result = $conn->query($recentQuery);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recentProcurements[] = $row;
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
</head>
<body>

<?php include '../partials/navbar.php'; ?>

<div class="container mt-5">
    <!-- Header + Quick Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Welcome, Procurement Officer</h3>

        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dashboardActions" data-bs-toggle="dropdown">
                <i class="bi bi-lightning-charge"></i> Quick Actions
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dashboardActions">
                <li><a class="dropdown-item" href="../suppliers/add_supplier.php"><i class="bi bi-building-add me-2"></i> Add Supplier</a></li>
                <li><a class="dropdown-item" href="../procurements/add_procurement.php"><i class="bi bi-cart-plus me-2"></i> New Procurement</a></li>
                <li><a class="dropdown-item" href="../procurements/list_procurements.php"><i class="bi bi-clipboard-data me-2"></i> View Procurements</a></li>
            </ul>
        </div>
    </div>

    <!-- Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-success text-center">
                <div class="card-body">
                    <i class="bi bi-building display-6"></i>
                    <h5 class="card-title mt-2">Total Suppliers</h5>
                    <p class="card-text fs-4"><?= $totalSuppliers ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-info text-center">
                <div class="card-body">
                    <i class="bi bi-box display-6"></i>
                    <h5 class="card-title mt-2">Total Procurements</h5>
                    <p class="card-text fs-4"><?= $totalProcurements ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Procurements -->
    <div class="card">
        <div class="card-header bg-light">
            <strong><i class="bi bi-clock-history me-2"></i> Recent Procurements</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Supplier</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentProcurements)): ?>
                        <?php foreach ($recentProcurements as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['ref']) ?></td>
                                <td><?= htmlspecialchars($item['supplier']) ?></td>
                                <td><?= htmlspecialchars($item['date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center">No recent procurements.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
