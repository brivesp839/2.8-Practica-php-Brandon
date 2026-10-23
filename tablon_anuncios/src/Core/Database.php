<?php
// El namespace debe reflejar la ubicación del archivo: src/Core/ → App\Core
// (antes era App\Config; hay que cambiarlo porque movimos el archivo)
namespace App\Core;

use PDO;

class Database
{
    // Aquí irá el código de la conexión, paso a paso
    // Aquí guardaremos el objeto de conexión.
    // "private": solo esta clase puede tocarlo directamente.
    private PDO $conexion;

    // El constructor se ejecuta automáticamente al hacer "new Database()"
    public function __construct()
    {
        // 1. Leemos los datos del .env con getenv().
        //    Las variables ya están disponibles en el contenedor (ver nota abajo).
        $servidor   = getenv('DB_HOST');      // Nombre del servicio en docker-compose ("db")
        $baseDatos  = getenv('DB_NAME');      // Nombre de la base de datos
        $usuario    = getenv('DB_USER');      // Usuario de MySQL
        $contrasena = getenv('DB_PASSWORD');  // Contraseña de ese usuario

        // 2. Construimos el DSN: la "dirección" de la base de datos.
        //    Indica el tipo (mysql), dónde está (host) y cuál usar (dbname).
        //    utf8mb4 permite tildes, ñ y emojis.
        $dsn = "mysql:host=$servidor;dbname=$baseDatos;charset=utf8mb4";

        // 3. Opciones de PDO: con ERRMODE_EXCEPTION, si una consulta falla,
        //    PDO lanza una excepción en lugar de fallar en silencio.
        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];

        // 4. Creamos la conexión y la guardamos en la propiedad de la clase.
        $this->conexion = new PDO($dsn, $usuario, $contrasena, $opciones);
    }
}