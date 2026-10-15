<?php

$host = "localhost";
$dbname = "immanuel_library";
$username = "root";
$password = "";

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
$option = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => FALSE
];

try {
  $pdo = new PDO($dsn, $username, $password, $option);
} catch (PDOException $e) {
  die("Koneksi ke database gagal: " . $e->getMessage());
}