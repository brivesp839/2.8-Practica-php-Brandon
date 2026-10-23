<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

// Si no salta ninguna excepción, la conexión funciona
new Database();
echo 'Conexión correcta';