<?php
namespace App\Controllers;

use App\Core\Database;
use App\Models\User;

class AuthController
{
    // Aquí guardamos el modelo de usuarios para usarlo en todas las acciones
    private User $modeloUsuario;

    public function __construct()
    {
        // Creamos la conexión a la base de datos...
        $bd = new Database();

        // ...y se la pasamos al modelo User, que ya sabe que trabaja con 'users'
        $this->modeloUsuario = new User($bd);
    }

    // GET /register: solo muestra el formulario de registro
    public function showRegisterForm(): void
    {
        // Aquí cargaremos la vista del formulario
    }

    // POST /register: recibe el formulario, valida los datos y crea el usuario
    public function register(): void
    {
        // Aquí validaremos y guardaremos al usuario con la contraseña hasheada
    }

    // GET /login: solo muestra el formulario de login
    public function showLoginForm(): void
    {
        // Aquí cargaremos la vista del formulario
    }

    // POST /login: recibe el formulario y comprueba las credenciales
    public function login(): void
    {
        // Aquí usaremos findByEmail() y, si es correcto, iniciaremos la sesión
    }

    // Cierra la sesión del usuario
    public function logout(): void
    {
        // Aquí destruiremos la sesión y redirigiremos
    }
}