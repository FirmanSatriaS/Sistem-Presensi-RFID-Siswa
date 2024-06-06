<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "../db.php";
global $conn;

// Melakukan query untuk mendapatkan semua data
$sql = "SELECT * FROM data";
$query = mysqli_query($conn, $sql) or die("Failed to fetch data: " . mysqli_error($conn));

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

// Membuat respons dalam format JSON
$response = [
    'status' => '200',
    'data' => $data
];

// Set headers dan kirim respons dalam format JSON
header('Content-Type: application/json');
echo json_encode($response);
