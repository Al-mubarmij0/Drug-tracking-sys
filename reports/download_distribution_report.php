<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';

// Set headers to force download as CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="stock_distribution_report.csv"');

$output = fopen('php://output', 'w');

// CSV column headers
fputcsv($output, ['#', 'Drug', 'Recipient', 'Quantity', 'Date Distributed']);

// Fetch data
$query = "
    SELECT s.drug_name, r.name AS recipient, d.quantity, d.date_distributed
    FROM distributions d
    JOIN stock s ON d.stock_id = s.id
    JOIN recipients r ON d.recipient_id = r.id
    ORDER BY d.date_distributed DESC
";
$result = $conn->query($query);
$counter = 1;

if ($result) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $counter++,
            $row['drug_name'],
            $row['recipient'],
            $row['quantity'],
            $row['date_distributed']
        ]);
    }
}

fclose($output);
exit;
?>
