<?php
// config/db.php

$host = 'localhost';
$dbname = 'pediadose';
$username = 'root'; // default XAMPP user
$password = ''; // default XAMPP password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage() . ". Pastikan Anda sudah membuat database 'pediadose' di phpMyAdmin.");
}
?>
