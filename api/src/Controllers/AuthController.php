<?php
namespace App\Controllers;

use App\Models\Auth\AuthModel;

class AuthController {
    private $db;

    public function __construct($db) {
        $this->db = $db;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function registro() {
        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos)) {
            $datos = [];
        }

        $nombre = trim((string) ($datos['nombre'] ?? ''));
        $email = trim((string) ($datos['email'] ?? ''));
        $password = (string) ($datos['password'] ?? '');
        $idCategoria = (int) ($datos['id_categoria'] ?? 0);

        if ($nombre === '' || $email === '' || $password === '') {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Nombre, correo y contraseña son obligatorios'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Correo inválido o contraseña de menos de 6 caracteres'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $authModel = new AuthModel($this->db);

        if ($authModel->buscarPorEmail($email)) {
            http_response_code(409);
            echo json_encode([
                'status' => 'error',
                'message' => 'Ese correo ya está registrado'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        if (!$authModel->crearUsuario($nombre, $email, $hash, $idCategoria ?: null)) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo registrar el usuario'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        http_response_code(201);
        echo json_encode([
            'status' => 'ok',
            'message' => 'Usuario registrado'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function login() {
        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos)) {
            $datos = [];
        }

        $email = trim((string) ($datos['email'] ?? ''));
        $password = (string) ($datos['password'] ?? '');

        if ($email === '' || $password === '') {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Correo y contraseña son obligatorios'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $authModel = new AuthModel($this->db);

        $fila = $authModel->buscarPorEmail($email);

        if (!$fila || !password_verify($password, $fila['password_hash'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Correo o contraseña incorrectos'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int) $fila['id_usuario'];
        $_SESSION['nombre'] = $fila['nombre'];
        $_SESSION['email'] = $fila['email'];
        $_SESSION['rol'] = $fila['nombre_rol'];

        echo json_encode([
            'status' => 'ok',
            'message' => 'Login correcto',
            'data' => [
                'nombre' => $fila['nombre'],
                'email' => $fila['email'],
                'rol' => $fila['nombre_rol']
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

    public function sesion() {
        if (empty($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'No hay sesión'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'data' => [
                'nombre' => $_SESSION['nombre'],
                'email' => $_SESSION['email'],
                'rol' => $_SESSION['rol']
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

    public function logout() {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        echo json_encode([
            'status' => 'ok',
            'message' => 'Sesión cerrada'
        ], JSON_UNESCAPED_UNICODE);
    }
}
