<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';

$page_title = "Manage Distributions";

// Pagination
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $perPage;

// Get total distributions for pagination
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM distributions");
$totalItems = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $perPage);

// Fetch paginated distributions with recipient and drug info
$distributions = [];
$query = "SELECT d.*, r.name AS recipient_name, s.drug_name FROM distributions d
          LEFT JOIN recipients r ON d.recipient_id = r.id
          LEFT JOIN stock s ON d.stock_id = s.id
          ORDER BY d.date_distributed DESC LIMIT $start, $perPage";
$res = $conn->query($query);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $distributions[] = $row;
    }
}

// Fetch recipients and stock list
$recipients = $conn->query("SELECT id, name FROM recipients ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$stock = $conn->query("SELECT id, drug_name FROM stock ORDER BY drug_name ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<?php include '../partials/navbar.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><?= $page_title ?></h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDistModal">
            <i class="bi bi-plus-circle"></i> Add Distribution
        </button>
    </div>

    <div class="mb-3">
        <a href="../dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
        </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search distributions...">

    <table class="table table-bordered table-striped" id="distTable">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Drug</th>
                <th>Recipient</th>
                <th>Quantity</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($distributions as $index => $d): ?>
            <tr>
                <td><?= $start + $index + 1 ?></td>
                <td><?= htmlspecialchars($d['drug_name']) ?></td>
                <td><?= htmlspecialchars($d['recipient_name']) ?></td>
                <td><?= (int) $d['quantity'] ?></td>
                <td><?= htmlspecialchars($d['date_distributed']) ?></td>
                <td>
                    <a href="delete_distribution.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('Delete this distribution?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Add Distribution Modal -->
<div class="modal fade" id="addDistModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="add_distribution.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Distribution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label>Drug</label>
                    <select name="stock_id" class="form-select" required>
                        <option value="">Select drug</option>
                        <?php foreach ($stock as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['drug_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Recipient</label>
                    <select name="recipient_id" class="form-select" required>
                        <option value="">Select recipient</option>
                        <?php foreach ($recipients as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Quantity</label>
                    <input type="number" name="quantity" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Date</label>
                    <input type="date" name="date_distributed" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="add" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    document.querySelectorAll("#distTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
    });
});
</script>
</body>
</html>
