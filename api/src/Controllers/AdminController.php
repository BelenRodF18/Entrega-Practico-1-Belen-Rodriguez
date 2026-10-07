<?php
namespace App\Controllers;

use App\Models\ArticuloModel;
use App\Models\UsuarioModel;

class AdminController {
    private $db;

    public function __construct($db) {
        $this->db = $db;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function esAdmin() {
        if (empty($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'No hay sesión'
            ], JSON_UNESCAPED_UNICODE);
            return false;
        }

        if (($_SESSION['rol'] ?? '') !== 'Administrador') {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'Acceso solo para administradores'
            ], JSON_UNESCAPED_UNICODE);
            return false;
        }

        return true;
    }

    public function resumen() {
        if (!$this->esAdmin()) {
            return;
        }

        $articuloModel = new ArticuloModel($this->db);
        $usuarioModel = new UsuarioModel($this->db);

        echo json_encode([
            'status' => 'ok',
            'data' => [
                'articulos' => $articuloModel->contar(),
                'disponibles' => $articuloModel->contarDisponibles(),
                'usuarios' => $usuarioModel->contarPorRol('Usuario'),
                'administradores' => $usuarioModel->contarPorRol('Administrador')
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

    public function articulos() {
        if (!$this->esAdmin()) {
            return;
        }

        $articuloModel = new ArticuloModel($this->db);
        $lista = $articuloModel->listar();

        if ($lista === false) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudieron leer los artículos (¿existe la tabla ARTICULOS?)'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'data' => $lista
        ], JSON_UNESCAPED_UNICODE);
    }
}
