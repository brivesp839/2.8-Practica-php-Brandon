<?php
namespace App\Models;

use App\Core\Database;
use InvalidArgumentException;

// "abstract": esta clase NO se puede instanciar directamente (new BaseModel() daría error).
// Solo sirve de plantilla para que otras clases (Article, Comment, User) hereden de ella.
abstract class BaseModel
{
    // "protected": accesibles desde esta clase y desde las que hereden de ella
    protected Database $bd;     // La conexión compartida, ya creada fuera
    protected string $tabla;    // Tabla con la que trabaja este modelo (ej: 'articles')

    // El constructor recibe la instancia de Database y el nombre de la tabla.
    // Las clases hijas llamarán a este constructor con parent::__construct(...)
    public function __construct(Database $bd, string $tabla)
    {
        $this->bd = $bd;
        $this->tabla = $tabla;
    }

    // ------------------------------------------------------------
    // getAll: devuelve TODOS los registros de la tabla
    // ------------------------------------------------------------
    public function getAll(): ?array
    {
        // Las comillas invertidas (`) rodean el nombre de la tabla por seguridad
        $sql = "SELECT * FROM `{$this->tabla}`";

        // No hay valores variables, por eso no pasamos parámetros
        return $this->bd->fetchAll($sql);
    }

    // ------------------------------------------------------------
    // getById: devuelve UN registro por su id (o null si no existe)
    // ------------------------------------------------------------
    public function getById(int $id): ?array
    {
        // El ? es un hueco que se rellenará con el valor de $id
        $sql = "SELECT * FROM `{$this->tabla}` WHERE id = ?";

        return $this->bd->fetchOne($sql, [$id]);
    }

    // ------------------------------------------------------------
    // delete: elimina el registro con ese id
    // ------------------------------------------------------------
    public function delete(int $id): void
    {
        $sql = "DELETE FROM `{$this->tabla}` WHERE id = ?";

        // No necesitamos leer filas, así que basta con query()
        $this->bd->query($sql, [$id]);
    }

    // ------------------------------------------------------------
    // insert: añade un registro a partir de un array campo => valor
    // ------------------------------------------------------------
    public function insert(array $datos): void
    {
        // Comprobamos que los nombres de campo son seguros (ver nota más abajo)
        $this->validarCampos($datos);

        // 1. Los nombres de los campos son las CLAVES del array
        //    ['email' => 'a@b.com', 'username' => 'ana']  →  ['email', 'username']
        $campos = array_keys($datos);

        // 2. Los unimos en un texto para el SQL: `email`, `username`
        $columnas = '`' . implode('`, `', $campos) . '`';

        // 3. Creamos un "?" por cada campo: "?, ?"
        //    array_fill(0, 2, '?') genera ['?', '?'] y implode los une con ", "
        $huecos = implode(', ', array_fill(0, count($campos), '?'));

        // 4. Montamos el SQL completo:
        //    INSERT INTO `users` (`email`, `username`) VALUES (?, ?)
        $sql = "INSERT INTO `{$this->tabla}` ($columnas) VALUES ($huecos)";

        // 5. Los valores son los VALORES del array, en el mismo orden que los campos
        $valores = array_values($datos);

        $this->bd->query($sql, $valores);
    }

    // ------------------------------------------------------------
    // update: modifica el registro $id con los pares campo => valor
    // ------------------------------------------------------------
    public function update(int $id, array $datos): void
    {
        $this->validarCampos($datos);

        // 1. Construimos cada trozo "campo = ?" del SET
        //    ['email' => '..', 'username' => '..']  →  ["`email` = ?", "`username` = ?"]
        $asignaciones = [];
        foreach (array_keys($datos) as $campo) {
            $asignaciones[] = "`$campo` = ?";
        }

        // 2. Los unimos con comas: `email` = ?, `username` = ?
        $listaAsignaciones = implode(', ', $asignaciones);

        // 3. SQL completo:
        //    UPDATE `users` SET `email` = ?, `username` = ? WHERE id = ?
        $sql = "UPDATE `{$this->tabla}` SET $listaAsignaciones WHERE id = ?";

        // 4. Los valores van en el mismo orden que los ?: primero los del SET
        //    y al final el id, que corresponde al ? del WHERE
        $valores = array_values($datos);
        $valores[] = $id;

        $this->bd->query($sql, $valores);
    }

    // ------------------------------------------------------------
    // validarCampos: método auxiliar PRIVADO (solo se usa dentro de esta clase)
    // ------------------------------------------------------------
    private function validarCampos(array $datos): void
    {
        // No tiene sentido insertar o actualizar sin datos
        if (empty($datos)) {
            throw new InvalidArgumentException('El array de datos está vacío');
        }

        foreach (array_keys($datos) as $campo) {
            // Solo permitimos letras, números y guion bajo en los nombres de campo
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $campo)) {
                throw new InvalidArgumentException("Nombre de campo no válido: $campo");
            }
        }
    }
}