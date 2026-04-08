<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "mseet_41474467_sagewood_books";

$conn = mysqli_connect($host, $user, $password, $database);
mysqli_set_charset($conn, "utf8mb4_general_ci");
if (!$conn) {
    die("Kết nối database thất bại: " . mysqli_connect_error());
}
