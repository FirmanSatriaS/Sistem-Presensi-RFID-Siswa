<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "../db.php";
global $conn;

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'];
$nama = $input['nama'];
$tag = $input['tag'];
date_default_timezone_set('Asia/Jakarta');
$tanggal = date("Y-m-d H:i:s");

$sql = "UPDATE data SET nama='$nama', tag='$tag', updatedAt='$tanggal' WHERE id=$id";
$query = mysqli_query($conn, $sql) or die("Error updating data " . mysqli_error($conn));

if (mysqli_affected_rows($conn) > 0) {
    $data_akhir = [
        'status' => '200',
        'message' => 'Data updated successfully',
        'data' => [
            'id' => $id,
            'nama' => $nama,
            'tag' => $tag,
            'updatedAt' => $tanggal
        ]
    ];
} else {
    $data_akhir = [
        'status' => '404',
        'message' => 'ID not found or no changes made'
    ];
}

header('Content-Type: application/json');
echo json_encode($data_akhir);
