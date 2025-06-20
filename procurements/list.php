<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';

$page_title = "Manage Procurements";

// Pagination
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $perPage;

// Get all suppliers for dropdowns
$suppliers = [];
$supRes = $conn->query("SELECT id, name FROM suppliers ORDER BY name ASC");
while ($row = $supRes->fetch_assoc()) {
    $suppliers[] = $row;
}

// Count total procurements
$totalResult = $conn->query("SELECT COUNT(*) as total FROM procurements");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $perPage);

// Get paginated procurements with supplier names
$procurements = [];
$sql = "SELECT p.*, s.name AS supplier_name 
        FROM procurements p 
        LEFT JOIN suppliers s ON p.supplier_id = s.id 
        ORDER BY p.date_procured DESC 
        LIMIT $start, $perPage";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $procurements[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include '../partials/navbar.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><?= $page_title ?></h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProcurementModal">
            <i class="bi bi-cart-plus"></i> Add Procurement
        </button>
    </div>

    <div class="mb-3">
        <a href="../dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <!-- Search -->
    <input type="text" class="form-control mb-3" id="searchInput" placeholder="Search procurements...">

    <!-- Table -->
    <table class="table table-bordered" id="procurementTable">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Supplier</th>
                <th>Date Procured</th>
                <th>Reference</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($procurements as $index => $p): ?>
            <tr>
                <td><?= $start + $index + 1 ?></td>
                <td><?= htmlspecialchars($p['supplier_name'] ?? 'Unknown') ?></td>
                <td><?= $p['date_procured'] ?></td>
                <td><?= htmlspecialchars($p['reference_no']) ?></td>
                <td><?= htmlspecialchars($p['notes']) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $p['id'] ?>">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <a href="delete_procurement.php?id=<?= $p['id'] ?>" onclick="return confirm('Delete this procurement?')" class="btn btn-sm btn-danger">
                        <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?= $p['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <form action="edit_procurement.php" method="POST" class="modal-content">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Procurement</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-2">
                                <label>Supplier</label>
                                <select name="supplier_id" class="form-select" required>
                                    <option value="">Select</option>
                                    <?php foreach ($suppliers as $s): ?>
                                        <option value="<?= $s['id'] ?>" <?= $s['id'] == $p['supplier_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($s['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label>Date Procured</label>
                                <input type="date" name="date_procured" class="form-control" value="<?= $p['date_procured'] ?>" required>
                            </div>
                            <div class="mb-2">
                                <label>Reference No</label>
                                <input type="text" name="reference_no" class="form-control" value="<?= htmlspecialchars($p['reference_no']) ?>">
                            </div>
                            <div class="mb-2">
                                <label>Notes</label>
                                <textarea name="notes" class="form-control"><?= htmlspecialchars($p['notes']) ?></textarea>
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

<!-- Add Procurement Modal -->
<div class="modal fade" id="addProcurementModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="add_procurement.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Procurement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label>Supplier</label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">Select</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Date Procured</label>
                    <input type="date" name="date_procured" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Reference No</label>
                    <input type="text" name="reference_no" class="form-control">
                </div>
                <div class="mb-2">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="add" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById("searchInput").addEventListener("input", function () {
    const value = this.value.toLowerCase();
    document.querySelectorAll("#procurementTable tbody tr").forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
    });
});
</script>
</body>
</html>
