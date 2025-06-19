<?php
require_once __DIR__ . '/../config.php';

// Handle POST request to update user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $role = $_POST['role'];

    $updateQuery = "UPDATE users SET username=?, role=?";
    $params = [$username, $role];
    $types = "ss";

    // Update password if provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $updateQuery .= ", password=?";
        $params[] = $password;
        $types .= "s";
    }

    $updateQuery .= " WHERE id=?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        header("Location: list.php?success=User+updated+successfully");
    } else {
        header("Location: list.php?error=Failed+to+update+user");
    }
    $stmt->close();
    exit;
}

// Handle GET request to show the form
if (!isset($_GET['id'])) {
    header("Location: list.php?error=No+user+ID+provided");
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: list.php?error=User+not+found");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">Edit User</h3>

    <form action="edit_user.php" method="POST" class="card p-4 shadow-sm">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control"
                   value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password (leave blank to keep existing)</label>
            <input type="password" name="password" id="password" class="form-control">
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" id="role" class="form-select" required>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="procurement" <?= $user['role'] === 'procurement' ? 'selected' : '' ?>>Procurement</option>
                <option value="pharmacist" <?= $user['role'] === 'pharmacist' ? 'selected' : '' ?>>Pharmacist</option>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="list.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" name="update" class="btn btn-primary">Update User</button>
        </div>
    </form>
</div>
</body>
</html>
