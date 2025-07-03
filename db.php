<?php


$host = "localhost";
$dbname = "amasyabus";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;port=3309;dbname=$dbname;charset=utf8", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   // echo "Veritabanı bağlantısı başarılı!";
} catch (PDOException $e) {
  //  echo "Bağlantı hatası: " . $e->getMessage(); // Detaylı hata mesajı
}
?>