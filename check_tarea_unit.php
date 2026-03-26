<?php
$host = "localhost";
$port = "5432";
$dbname = "control_produccion";
$user = "postgres";
$password = "grupoes2026";

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
$pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->query("SELECT id, nombre, horas_estimadas FROM tarea LIMIT 5");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
