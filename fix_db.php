<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
$loader = require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();
require_once __DIR__ . '/vendor/codeigniter4/framework/system/Test/bootstrap.php';

$db = \Config\Database::connect();
$forge = \Config\Database::forge();

$fields = [
    'fecha_inicio_manual' => [
        'type' => 'DATE',
        'null' => true,
    ],
    'hora_inicio_manual' => [
        'type' => 'TIME',
        'null' => true,
    ],
];

try {
    $forge->addColumn('actividades', $fields);
    echo "Columnas agregadas correctamente a 'actividades'";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
