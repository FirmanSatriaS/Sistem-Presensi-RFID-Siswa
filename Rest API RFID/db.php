<?php

$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'project_iot';

$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
if (!$conn) {
    die('Gagal menghubungkan ke database: ' . mysqli_connect_error());
}