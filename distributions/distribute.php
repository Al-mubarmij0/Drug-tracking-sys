<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';

$page_title = "Distribute Drug";

// Fetch stock options
$stockOptions = [];
$stockQuery = "SELECT id, drug_name, batch_number, quantity FROM stock WHERE quantity > 0 ORDER BY drug_name";
$stockResult = $conn->query($stockQuery);
if ($stockResult) {
    while ($row = $stockResult->fetch_assoc()) {
        $stockOptions[] = $row;
    }
}

// Fetch recipients
$recipients = [];
$recipientResult = $conn->query("SELECT id, name FROM recipients ORDER BY name");
if ($recipientResult) {
    while ($row = $recipientResult->fetch_assoc()) {
        $recipients[] = $row;
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
    <h3 class="mb-4"><?= $page_title ?></h3>

    <form method="POST" action="add.php" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="stock_id" class="form-label">Select Stock</label>
            <select name="stock_id" id="stock_id" class="form-select" required>
                <option value="">-- Select Drug --</option>
                <?php foreach ($stockOptions as $stock): ?>
                    <option value="<?= $stock['id'] ?>">
                        <?= htmlspecialchars($stock['drug_name']) ?> (Batch: <?= $stock['batch_number'] ?>, Qty: <?= $stock['quantity'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="recipient_id" class="form-label">Recipient</label>
            <select name="recipient_id" id="recipient_id" class="form-select" required>
                <option value="">-- Select Recipient --</option>
                <?php foreach ($recipients as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label for="date_distributed" class="form-label">Date</label>
            <input type="date" name="date_distributed" id="date_distributed" class="form-control" required value="<?= date('Y-m-d') ?>">
        </div>

        <button type="submit" name="add" class="btn btn-primary">Distribute</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
