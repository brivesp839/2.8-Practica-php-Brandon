<?php

// El namespace debe reflejar la ubicación del archivo: src/Core/ → App\Core
// (antes era App\Config; hay que cambiarlo porque movimos el archivo)
namespace App\Core;

// Importo PDO y PDOStatement
use PDO;
use PDOStatement;   // Lo necesitamos para indicar el tipo de retorno de query()

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

    // Prepara y ejecuta CUALQUIER consulta SQL de forma segura.
    // Devuelve el "statement" (la sentencia ya ejecutada).
    public function query(string $sql, array $parametros = []): PDOStatement
    {
        // 1. prepare() envía la consulta a MySQL con "huecos" (los ?) en lugar
        //    de valores. Así los datos del usuario nunca se mezclan con el SQL.
        $sentencia = $this->conexion->prepare($sql);

        // 2. execute() rellena los huecos, en orden, con los valores del array
        $sentencia->execute($parametros);

        // 3. Devolvemos la sentencia para que otros métodos puedan leer el resultado
        return $sentencia;
    }

    // Devuelve TODAS las filas del resultado, como array de arrays asociativos.
    public function fetchAll(string $sql, array $parametros = []): ?array
    {
        $sentencia = $this->query($sql, $parametros);

        // FETCH_ASSOC: cada fila es ['id' => 1, 'title' => '...'] (claves = nombre de columna)
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Devuelve UNA sola fila (la primera), o null si no hay ninguna.
    public function fetchOne(string $sql, array $parametros = []): ?array
    {
        $sentencia = $this->query($sql, $parametros);

        $fila = $sentencia->fetch(PDO::FETCH_ASSOC);

        // fetch() devuelve false cuando no hay resultados.
        // Lo convertimos en null, que es lo que indica el tipo de retorno ?array.
        return $fila === false ? null : $fila;
    }
}
