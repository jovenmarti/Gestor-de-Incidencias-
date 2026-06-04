<?php
$host = 'db';
$dbname = 'incidencias';
$user = 'admin';
$password = 'admin123';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Conexión fallida: ' . $e->getMessage()]));
}