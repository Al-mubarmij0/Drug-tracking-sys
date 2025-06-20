<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';

$page_title = "System Logs";

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $perPage;

// Get total logs for pagination
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM logs");
$totalLogs = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalLogs / $perPage);

// Fetch logs with user info
$logs = [];
$query = "SELECT logs.*, users.username 
          FROM logs 
          LEFT JOIN users ON logs.user_id = users.id 
          ORDER BY logs.timestamp DESC 
          LIMIT $start, $perPage";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<?php include '../partials/navbar.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><?= $page_title ?></h3>
        <a href="../dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search logs...">

    <table class="table table-bordered table-striped" id="logsTable">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Action</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $index => $log): ?>
                <tr>
                    <td><?= $start + $index + 1 ?></td>
                    <td><?= htmlspecialchars($log['username'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($log['action']) ?></td>
                    <td><?= date('Y-m-d H:i:s', strtotime($log['timestamp'])) ?></td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    document.querySelectorAll("#logsTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
    });
});
</script>
</body>
</html>
