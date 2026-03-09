<?php
require 'vendor/autoload.php';
$db = \Config\Database::connect();
$fields = $db->getFieldNames('actividades');
foreach ($fields as $field) {
    echo $field . "\n";
}
