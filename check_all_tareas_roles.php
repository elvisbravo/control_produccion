<?php
$host = "localhost";
$port = "5432";
$dbname = "control_produccion";
$user = "postgres";
$password = "grupoes2026";

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
try {
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "--- Tareas Roles for ALL tasks ---\n";
    $stmt = $pdo->prepare("SELECT * FROM tareas_roles");
    $stmt->execute();
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    echo $e->getMessage();
}
