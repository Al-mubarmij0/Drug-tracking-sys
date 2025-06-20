<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';

$page_title = "Manage Suppliers";

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $perPage;

// Total for pagination
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM suppliers");
$totalSuppliers = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalSuppliers / $perPage);

// Fetch suppliers
$suppliers = [];
$result = $conn->query("SELECT * FROM suppliers ORDER BY id DESC LIMIT $start, $perPage");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
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
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <h3><?= $page_title ?></h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
            <i class="bi bi-plus-circle"></i> Add Supplier
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

    <!-- Search -->
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search suppliers...">

    <!-- Supplier Table -->
    <table class="table table-bordered table-striped align-middle" id="supplierTable">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Contact Info</th>
                <th style="width: 120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suppliers as $index => $supplier): ?>
                <tr>
                    <td><?= $start + $index + 1 ?></td>
                    <td><?= htmlspecialchars($supplier['name']) ?></td>
                    <td><?= nl2br(htmlspecialchars($supplier['contact_info'])) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSupplierModal<?= $supplier['id'] ?>">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <a href="delete_supplier.php?id=<?= $supplier['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this supplier?');">
                            <i class="bi bi-trash"></i>
                        </a>
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

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="add_supplier.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Contact Info</label>
                    <textarea name="contact_info" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="add" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modals after table -->
<?php foreach ($suppliers as $supplier): ?>
    <div class="modal fade" id="editSupplierModal<?= $supplier['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <form action="edit_supplier.php" method="POST" class="modal-content">
                <input type="hidden" name="id" value="<?= $supplier['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($supplier['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Contact Info</label>
                        <textarea name="contact_info" class="form-control" rows="3"><?= htmlspecialchars($supplier['contact_info']) ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update" class="btn btn-success">Update</button>
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
    const rows = document.querySelectorAll("#supplierTable tbody tr");
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>
</body>
</html>
