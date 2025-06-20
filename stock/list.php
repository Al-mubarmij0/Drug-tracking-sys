<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';

$page_title = "Manage Stock";

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $perPage;

// Get total rows
$totalRes = $conn->query("SELECT COUNT(*) AS total FROM stock");
$totalItems = $totalRes->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $perPage);

// Fetch paginated stock with procurement and supplier info
$stock = [];
$query = "SELECT s.*, p.id AS procurement_ref, sup.name AS supplier_name
          FROM stock s
          LEFT JOIN procurements p ON s.procurement_id = p.id
          LEFT JOIN suppliers sup ON p.supplier_id = sup.id
          ORDER BY s.expiry_date ASC
          LIMIT $start, $perPage";
$res = $conn->query($query);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $stock[] = $row;
    }
}

// Fetch procurements for dropdown
$procurements = [];
$prcRes = $conn->query("SELECT p.id, s.name AS supplier FROM procurements p LEFT JOIN suppliers s ON p.supplier_id = s.id ORDER BY p.id DESC");
if ($prcRes) {
    while ($p = $prcRes->fetch_assoc()) {
        $procurements[] = $p;
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
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
            <i class="bi bi-plus-circle"></i> Add Stock
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

    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search stock...">

    <table class="table table-bordered table-striped align-middle" id="stockTable">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Drug</th>
                <th>Batch</th>
                <th>Quantity</th>
                <th>Expiry</th>
                <th>Procurement</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stock as $index => $item): ?>
                <tr>
                    <td><?= $start + $index + 1 ?></td>
                    <td><?= htmlspecialchars($item['drug_name']) ?></td>
                    <td><?= htmlspecialchars($item['batch_number']) ?></td>
                    <td><?= (int) $item['quantity'] ?></td>
                    <td><?= date('Y-m-d', strtotime($item['expiry_date'])) ?></td>
                    <td>
                        <?= $item['procurement_ref'] 
                            ? 'Ref#' . $item['procurement_ref'] . ' (' . htmlspecialchars($item['supplier_name']) . ')' 
                            : '—' ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editStockModal<?= $item['id'] ?>">
                            Edit
                        </button>
                        <a href="delete_stock.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this stock item?')">Delete</a>
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

<!-- Add Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="add_stock.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label>Drug Name</label>
                    <input type="text" name="drug_name" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Batch Number</label>
                    <input type="text" name="batch_number" class="form-control">
                </div>
                <div class="mb-2">
                    <label>Quantity</label>
                    <input type="number" name="quantity" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Procurement</label>
                    <select name="procurement_id" class="form-select">
                        <option value="">—</option>
                        <?php foreach ($procurements as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= 'Ref#' . $p['id'] . ' - ' . htmlspecialchars($p['supplier']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="add" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modals -->
<?php foreach ($stock as $item): ?>
    <div class="modal fade" id="editStockModal<?= $item['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <form action="edit_stock.php" method="POST" class="modal-content">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Drug Name</label>
                        <input type="text" name="drug_name" class="form-control" value="<?= htmlspecialchars($item['drug_name']) ?>" required>
                    </div>
                    <div class="mb-2">
                        <label>Batch Number</label>
                        <input type="text" name="batch_number" class="form-control" value="<?= htmlspecialchars($item['batch_number']) ?>">
                    </div>
                    <div class="mb-2">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="<?= $item['quantity'] ?>" required>
                    </div>
                    <div class="mb-2">
                        <label>Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" value="<?= $item['expiry_date'] ?>" required>
                    </div>
                    <div class="mb-2">
                        <label>Procurement</label>
                        <select name="procurement_id" class="form-select">
                            <option value="">—</option>
                            <?php foreach ($procurements as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $item['procurement_id'] == $p['id'] ? 'selected' : '' ?>>
                                    <?= 'Ref#' . $p['id'] . ' - ' . htmlspecialchars($p['supplier']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
    document.querySelectorAll("#stockTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
    });
});
</script>
</body>
</html>
