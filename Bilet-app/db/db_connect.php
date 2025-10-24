<?php
$host = 'db';
$dbname = 'bilet_db';
$user = 'root';
$pass = 'root';

try {
  $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (PDOException $e) {
  die("DB bağlantı hatası: " . $e->getMessage());
}
