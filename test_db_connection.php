<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'munu_db';
$port = 3306;

$conn = new mysqli($hostname, $username, $password, $database, $port);

if ($conn->connect_error) {
    echo "Bağlantı hatası: " . $conn->connect_error;
} else {
    echo "Veritabanına başarıyla bağlandı!";
    $conn->close();
}
?>