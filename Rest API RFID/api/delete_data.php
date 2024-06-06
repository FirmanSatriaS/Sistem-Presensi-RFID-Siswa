<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "../db.php";
global $conn;

// Mengambil nilai 'id' dari parameter GET
if (isset($_GET['id'])) {
    // Memastikan nilai 'id' adalah angka
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id === false || $id === null) {
        $data_akhir = [
            'status' => '400',
            'message' => 'Invalid ID format'
        ];
    } else {
        // Melakukan query untuk menghapus data berdasarkan id
        $sql = "DELETE FROM data WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        // Memeriksa apakah ada baris yang terpengaruh oleh operasi DELETE
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            // Jika ada baris yang terhapus (affected row > 0)
            $data_akhir = [
                'status' => '200',
                'message' => 'Data deleted successfully'
            ];
        } else {
            // Jika tidak ada baris yang terhapus (affected row == 0)
            $data_akhir = [
                'status' => '200', // Ubah status menjadi '200'
                'message' => 'Data ID not found' // Pesan khusus untuk data tidak ditemukan
            ];
        }
        mysqli_stmt_close($stmt);
    }
} else {
    // Jika parameter 'id' tidak disediakan dalam request
    $data_akhir = [
        'status' => '400',
        'message' => 'Parameter ID is missing'
    ];
}

// Set headers dan kirim respons dalam format JSON
header('Content-Type: application/json');
echo json_encode($data_akhir);
