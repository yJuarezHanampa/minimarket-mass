<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/UsuarioRepository.php';

class AuthController {

    public function mostrarLogin(string $error = ''): void {
        require __DIR__ . '/../views/auth/login.php';
    }

    public function procesarLogin(): void {

        // Ejercicio 3: inicializar contador si no existe
        if (!isset($_SESSION['intentos_fallidos'])) {
            $_SESSION['intentos_fallidos'] = 0;
        }

        // Ejercicio 3: bloquear si ya falló 3 veces
        if ($_SESSION['intentos_fallidos'] >= 3) {
            $this->mostrarLogin('⛔ Demasiados intentos. Recarga la página para reintentar.');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $this->mostrarLogin('Completa usuario y contraseña.');
            return;
        }

        $repo    = new UsuarioRepository();
        $usuario = $repo->buscarPorUsername($username);

        if ($usuario === null || !$usuario->verificarPassword($password)) {

            // Ejercicio 3: sumar intento fallido
            $_SESSION['intentos_fallidos']++;
            $restantes = 3 - $_SESSION['intentos_fallidos'];

            $error = $restantes > 0
                ? "Usuario o contraseña incorrectos. Te quedan $restantes intento(s)."
                : '⛔ Demasiados intentos. Recarga la página para reintentar.';

            $this->mostrarLogin($error);
            return;
        }

        // Login correcto: resetear contador
        $_SESSION['intentos_fallidos'] = 0;

        $_SESSION['usuario'] = [
            'id'       => $usuario->getId(),
            'username' => $usuario->getUsername(),
            'nombre'   => $usuario->getNombreCompleto(),
            'rol'      => $usuario->getRol(),
            'tienda'   => $usuario->getTienda(),
        ];

        header('Location: index.php?accion=catalogo');
        exit;
    }

    public function logout(): void {
        $_SESSION = [];
        session_destroy();
        header('Location: index.php?accion=login');
        exit;
    }
}