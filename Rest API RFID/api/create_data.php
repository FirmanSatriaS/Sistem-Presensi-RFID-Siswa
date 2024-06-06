<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json');

require_once "../db.php";
global $conn;

$input = json_decode(file_get_contents('php://input'), true);
if ($input === null) {
    echo json_encode(['status' => '400', 'message' => 'Invalid JSON']);
    exit;
}

$nama = $input['nama'];
$tag = $input['tag'];
date_default_timezone_set('Asia/Jakarta');
$tanggal = date("Y-m-d H:i:s");

$sql = "INSERT INTO data (nama ,tag, createdAt) VALUES ('$nama', '$tag', '$tanggal')";
$query = mysqli_query($conn, $sql) or die(json_encode(['status' => '500', 'message' => 'Error Inserting data ' . mysqli_error($conn)]));

$data_akhir = [
    'status' => '201',
    'message' => 'Data Created Successfully',
    'data' => [
        'nama' => $nama,
        'tag' => $tag,    
        'createdAt' => $tanggal
    ]
];

echo json_encode($data_akhir);
