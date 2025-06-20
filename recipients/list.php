<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';

$page_title = "Manage Recipients";

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $perPage;

// Get total count
$totalRes = $conn->query("SELECT COUNT(*) AS total FROM recipients");
$totalItems = $totalRes->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $perPage);

// Fetch paginated recipients
$recipients = [];
$res = $conn->query("SELECT * FROM recipients ORDER BY name ASC LIMIT $start, $perPage");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $recipients[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../partials/navbar.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <h3><?= $page_title ?></h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRecipientModal">
            <i class="bi bi-plus-circle"></i> Add Recipient
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

    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search recipients...">

    <table class="table table-bordered table-striped align-middle" id="recipientTable">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Identifier</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($recipients as $index => $r): ?>
            <tr>
                <td><?= $start + $index + 1 ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['identifier']) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editRecipientModal<?= $r['id'] ?>">Edit</button>
                    <a href="delete_recipient.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this recipient?')">Delete</a>
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

<!-- Add Modal -->
<div class="modal fade" id="addRecipientModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="add_recipient.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Recipient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Identifier</label>
                    <input type="text" name="identifier" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modals -->
<?php foreach ($recipients as $r): ?>
<div class="modal fade" id="editRecipientModal<?= $r['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <form action="edit_recipient.php" method="POST" class="modal-content">
            <input type="hidden" name="id" value="<?= $r['id'] ?>">
            <div class="modal-header">
                <h5 class="modal-title">Edit Recipient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($r['name']) ?>" required>
                </div>
                <div class="mb-2">
                    <label>Identifier</label>
                    <input type="text" name="identifier" class="form-control" value="<?= htmlspecialchars($r['identifier']) ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Update</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    document.querySelectorAll("#recipientTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
    });
});
</script>
</body>
</html>
