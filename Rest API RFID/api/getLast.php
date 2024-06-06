<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "../db.php";
global $conn;

$sql = "SELECT * FROM data ORDER BY id DESC LIMIT 1";
$query = mysqli_query($conn, $sql) or die("Error fetching last data: " . mysqli_error($conn));

$data_akhir = [];
if (mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);
    $data_akhir = [
        'status' => '200',
        'message' => 'Successful request',
        'data' => $row
    ];
} else {
    $data_akhir = [
        'status' => '404',
        'message' => 'Not Found'
    ];
}

header('Content-Type: application/json');
echo json_encode($data_akhir);