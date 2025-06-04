<?php
// Include your DB connection here
// For example:
// require 'db_connection.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="selected_participants.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// CSV column headers
fputcsv($output, ['Leader Name', 'Mobile Number', 'Email', 'Project Repo', 'Status']);

// Fetch selected participants from database
$sql = "SELECT leaderName, leaderPhone, leaderEmail, projectRepo, status FROM teams WHERE status = 'Selected'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['leaderName'],
            $row['leaderPhone'],
            $row['leaderEmail'],
            $row['projectRepo'],
            $row['status']
        ]);
    }
}

fclose($output);
exit;
