<?php
$host = "localhost";
$port = "5432";
$dbname = "control_produccion";
$user = "postgres";
$password = "grupoes2026";

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
try {
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "--- Tareas Roles for tarea_id 2 ---\n";
    $stmt = $pdo->prepare("SELECT * FROM tareas_roles WHERE tarea_id = 2");
    $stmt->execute();
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

    echo "\n--- Usuarios linked to those roles ---\n";
    $stmt = $pdo->prepare("SELECT u.id, u.usuario, u.rol_id, u.estado, p.nombres, p.apellidos 
                           FROM usuarios u 
                           JOIN personas p ON u.persona_id = p.id 
                           WHERE u.rol_id IN (SELECT rol_id FROM tareas_roles WHERE tarea_id = 2)");
    $stmt->execute();
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

    echo "\n--- Tareas Usuarios for tarea_id 2 ---\n";
    $stmt = $pdo->prepare("SELECT * FROM tareas_usuarios WHERE tarea_id = 2");
    $stmt->execute();
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    echo $e->getMessage();
}
