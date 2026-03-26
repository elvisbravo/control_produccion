<?php
$host = "localhost";
$port = "5432";
$dbname = "control_produccion";
$user = "postgres";
$password = "grupoes2026";

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
try {
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "Usuarios con rol 8 (sin JOIN):\n";
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE rol_id = 8");
    $stmt->execute();
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    echo $e->getMessage();
}
