<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "../db.php";
global $conn;

// Memeriksa apakah parameter 'delete_all' ada dalam request
if (isset($_GET['delete_all'])) {
    // Melakukan query untuk menghapus semua data di tabel 'data'
    $sql = "DELETE FROM data";
    $query = mysqli_query($conn, $sql) or die("Failed to delete all data: " . mysqli_error($conn));
    
    // Memeriksa apakah ada baris yang terpengaruh oleh operasi DELETE
    if (mysqli_affected_rows($conn) > 0) {
        // Jika ada baris yang terhapus (affected row > 0)
        $data_akhir = [
            'status' => '200',
            'message' => 'All data deleted successfully'
        ];
    } else {
        // Jika tidak ada baris yang terhapus (affected row == 0)
        $data_akhir = [
            'status' => '404',
            'message' => 'No data found to delete'
        ];
    }
} else {
    // Jika parameter 'delete_all' tidak disediakan dalam request
    $data_akhir = [
        'status' => '400',
        'message' => 'Parameter delete_all is missing'
    ];
}

// Set headers dan kirim respons dalam format JSON
header('Content-Type: application/json');
echo json_encode($data_akhir);
