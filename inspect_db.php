<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
$loader = require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/codeigniter4/framework/system/Test/bootstrap.php';

$db = \Config\Database::connect();
$query = $db->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'actividades'");
$results = $query->getResultArray();
foreach ($results as $row) {
    echo $row['column_name'] . ": " . $row['data_type'] . "\n";
}
echo "--- historial_estados_prospecto ---\n";
$query = $db->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'historial_estados_prospecto'");
$results = $query->getResultArray();
foreach ($results as $row) {
    echo $row['column_name'] . ": " . $row['data_type'] . "\n";
}
echo "--- DONE ---\n";
