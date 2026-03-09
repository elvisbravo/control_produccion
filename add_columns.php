<?php
try {
    require 'app/Config/Database.php';
    $db = \Config\Database::connect();
    $db->query("ALTER TABLE actividades ADD COLUMN fecha_inicio_manual DATE");
    $db->query("ALTER TABLE actividades ADD COLUMN hora_inicio_manual TIME");
    echo "Columnas agregadas correctamente";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
