<?php
namespace App\Models;

use App\Core\Database;

// "extends": User hereda todos los métodos de BaseModel
// (getAll, getById, insert, update, delete) sin escribirlos otra vez
class User extends BaseModel
{
    public function __construct(Database $bd)
    {
        // Llamamos al constructor de BaseModel indicándole la tabla 'users'.
        // Así, este modelo solo trabaja con esa tabla.
        parent::__construct($bd, 'users');
    }

    // Aquí irán los métodos propios de los usuarios, paso a paso
    // Comprueba si existe un usuario con ese email Y esa contraseña.
    // Devuelve el id del usuario si todo es correcto, o null si no.
    // IMPORTANTE: $password es la contraseña que escribió el usuario en el
    // formulario (en texto normal), no un hash.
    public function findByEmail(string $email, string $password): ?int
    {
        // 1. Buscamos al usuario SOLO por su email.
        //    Pedimos únicamente las columnas que necesitamos: el id y el hash guardado.
        $sql = 'SELECT id, password_hash FROM users WHERE email = ?';
        $usuario = $this->bd->fetchOne($sql, [$email]);

        // 2. Si fetchOne devolvió null, no existe ningún usuario con ese email
        if ($usuario === null) {
            return null;
        }

        // 3. password_verify() compara la contraseña escrita con el hash guardado
        //    en la base de datos. Devuelve true si coinciden y false si no.
        $contrasenaCorrecta = password_verify($password, $usuario['password_hash']);

        // 4. Si la contraseña no coincide, tampoco dejamos pasar
        if (!$contrasenaCorrecta) {
            return null;
        }

        // 5. Todo correcto: devolvemos el id.
        //    (int) lo convierte a número entero, porque la base de datos
        //    puede entregarlo como texto.
        return (int) $usuario['id'];
    }
}