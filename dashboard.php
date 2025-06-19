<?php
// dashboard.php (MAIN ENTRY POINT)
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session_check.php';

$role = $_SESSION['role'] ?? null;

switch ($role) {
    case 'admin':
        header("Location: admin/dashboard.php");
        exit;
    case 'procurement':
        header("Location: procurement/dashboard.php");
        exit;
    case 'pharmacist':
        header("Location: pharmacist/dashboard.php");
        exit;
    default:
        echo "Unauthorized access.";
        exit;
}
