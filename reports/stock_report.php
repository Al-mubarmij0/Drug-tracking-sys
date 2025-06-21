<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';

$page_title = "Stock Distribution Report";

// Fetch data
$report = [];
$query = "
    SELECT d.id, s.drug_name, r.name AS recipient, d.quantity, d.date_distributed
    FROM distributions d
    JOIN stock s ON d.stock_id = s.id
    JOIN recipients r ON d.recipient_id = r.id
    ORDER BY d.date_distributed DESC
";
$res = $conn->query($query);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $report[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $page_title ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (required for navbar icons) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../partials/navbar.php'; ?>

<div class="container mt-5">
    <h3><?= $page_title ?></h3>
    <div class="mb-3 d-flex justify-content-between">
        <a href="../pharmacist/dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
        </a>
        <a href="download_distribution_report.php" class="btn btn-success">
            <i class="bi bi-download"></i> Download CSV
        </a>
    </div>


    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Drug</th>
                    <th>Recipient</th>
                    <th>Quantity</th>
                    <th>Date Distributed</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($report)): ?>
                    <?php foreach ($report as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($item['drug_name']) ?></td>
                            <td><?= htmlspecialchars($item['recipient']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= $item['date_distributed'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No distributions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
