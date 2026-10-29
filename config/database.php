<?php
$host = "localhost";
$root = "root";
$pass = "";
$dbname = "immanuel_library"; #nama database yang digunakan, harus sama persis dengan nama database yang ada di phpmyadmin, jika tidak maka akan error tapi karena ada try catch maka akan menampilkan pesan errornya

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, #biar tau errornya apa dan kenapa
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, #mengembalikkan array asosiatif
    PDO::ATTR_EMULATE_PREPARES => false #mengatur siapa yang ekseuksinya, php atau mysql (false = mysql, true = php)
];
#=> digunakan saat berurusan dengan array, kalau -> berurusan dengan object, jadi kalau array pakai =>, kalau object pakai ->

try {
    $pdo = new PDO($dsn, $root, $pass, $options);
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}

?>