<?php
$host = "localhost";
$port = "5432";
$dbname = "control_produccion";
$user = "postgres";
$password = "grupoes2026";

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
try {
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "Total usuarios: " . $pdo->query("SELECT count(*) FROM usuarios")->fetchColumn() . "\n";
    
    echo "Usuarios con rol 6 (autorizado p/ tarea 2):\n";
    $stmt = $pdo->prepare("SELECT u.id, u.usuario, p.nombres FROM usuarios u LEFT JOIN personas p ON p.id = u.persona_id WHERE u.rol_id = 6");
    $stmt->execute();
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

    echo "Usuarios con rol 8 (autorizado p/ tarea 2):\n";
    $stmt = $pdo->prepare("SELECT u.id, u.usuario, p.nombres FROM usuarios u LEFT JOIN personas p ON p.id = u.persona_id WHERE u.rol_id = 8");
    $stmt->execute();
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

    echo "Usuarios en tareas_usuarios p/ tarea 2:\n";
    $stmt = $pdo->prepare("SELECT tu.usuario_id, tu.activo, p.nombres FROM tareas_usuarios tu LEFT JOIN usuarios u ON u.id = tu.usuario_id LEFT JOIN personas p ON p.id = u.persona_id WHERE tu.tarea_id = 2");
    $stmt->execute();
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    echo $e->getMessage();
}
