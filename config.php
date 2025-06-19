<?php
// config.php
$host = "localhost";
$user = "root";
$pass = ""; // Change if needed
$dbname = "drug_tracking";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}
?>
