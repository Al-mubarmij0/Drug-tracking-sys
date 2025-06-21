<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';

$page_title = "All Stock Items";
$stockList = [];

$sql = "
    SELECT s.id, s.drug_name, s.batch_number, s.quantity, s.expiry_date, p.reference_no
    FROM stock s
    LEFT JOIN procurements p ON s.procurement_id = p.id
    ORDER BY s.expiry_date ASC
";

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $stockList[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<?php include '../partials/navbar.php'; ?>

<div class="container mt-5">
    <h3><?= $page_title ?></h3>

    <div class="mb-3">
        <a href="../pharmacist/dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
        </a>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Drug Name</th>
                    <th>Batch No</th>
                    <th>Quantity</th>
                    <th>Expiry Date</th>
                    <th>Procurement Ref</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($stockList)): ?>
                    <?php foreach ($stockList as $i => $stock): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($stock['drug_name']) ?></td>
                            <td><?= htmlspecialchars($stock['batch_number']) ?></td>
                            <td><?= $stock['quantity'] ?></td>
                            <td><?= $stock['expiry_date'] ?></td>
                            <td><?= $stock['reference_no'] ?? 'N/A' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No stock items available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
