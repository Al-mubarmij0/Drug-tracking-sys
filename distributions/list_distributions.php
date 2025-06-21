<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';

$page_title = "All Distributions";
$distributions = [];

$sql = "
    SELECT d.id, s.drug_name, r.name AS recipient, d.quantity, d.date_distributed
    FROM distributions d
    JOIN stock s ON d.stock_id = s.id
    JOIN recipients r ON d.recipient_id = r.id
    ORDER BY d.date_distributed DESC
";

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $distributions[] = $row;
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
                    <th>Drug</th>
                    <th>Recipient</th>
                    <th>Quantity</th>
                    <th>Date Distributed</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($distributions)): ?>
                    <?php foreach ($distributions as $i => $dist): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($dist['drug_name']) ?></td>
                            <td><?= htmlspecialchars($dist['recipient']) ?></td>
                            <td><?= $dist['quantity'] ?></td>
                            <td><?= $dist['date_distributed'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No distributions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
