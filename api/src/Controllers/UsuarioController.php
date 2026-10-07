<?php
namespace App\Controllers;

use App\Models\ArticuloModel;
use App\Models\PrestamoModel;
use App\Models\UsuarioModel;

class UsuarioController {
    private $db;

    public function __construct($db) {
        $this->db = $db;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function haySesion() {
        if (empty($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'No hay sesión'
            ], JSON_UNESCAPED_UNICODE);
            return false;
        }

        return true;
    }

    private function error($codigo, $mensaje) {
        http_response_code($codigo);
        echo json_encode([
            'status' => 'error',
            'message' => $mensaje
        ], JSON_UNESCAPED_UNICODE);
    }

    public function perfil() {
        if (!$this->haySesion()) {
            return;
        }

        $usuarioModel = new UsuarioModel($this->db);
        $perfil = $usuarioModel->buscarPorId($_SESSION['usuario_id']);

        if (!$perfil) {
            $this->error(500, 'No se pudo leer el perfil');
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'data' => $perfil
        ], JSON_UNESCAPED_UNICODE);
    }

    public function prestamos() {
        if (!$this->haySesion()) {
            return;
        }

        $prestamoModel = new PrestamoModel($this->db);
        $lista = $prestamoModel->listarPorUsuario($_SESSION['usuario_id']);

        if ($lista === false) {
            $this->error(500, 'No se pudieron leer los préstamos');
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'data' => $lista
        ], JSON_UNESCAPED_UNICODE);
    }

    public function pedirPrestamo() {
        if (!$this->haySesion()) {
            return;
        }

        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos)) {
            $datos = [];
        }

        $idArticulo = (int) ($datos['id_articulo'] ?? 0);
        if ($idArticulo <= 0) {
            $this->error(400, 'Falta el artículo');
            return;
        }

        $articuloModel = new ArticuloModel($this->db);
        $articulo = $articuloModel->buscarDisponible($idArticulo);
        if (!$articulo) {
            $this->error(400, 'El artículo no está disponible');
            return;
        }

        $prestamoModel = new PrestamoModel($this->db);
        if ($prestamoModel->tieneActivo($_SESSION['usuario_id'], $idArticulo)) {
            $this->error(400, 'Ya tiene este artículo en préstamo');
            return;
        }

        $id = $prestamoModel->crear($_SESSION['usuario_id'], $idArticulo);
        if (!$id) {
            $this->error(500, 'No se pudo registrar el préstamo');
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'message' => 'Préstamo registrado: ' . $articulo['titulo'],
            'data' => ['id_prestamo' => $id]
        ], JSON_UNESCAPED_UNICODE);
    }

    public function devolverPrestamo($idPrestamo) {
        if (!$this->haySesion()) {
            return;
        }

        $prestamoModel = new PrestamoModel($this->db);
        $filas = $prestamoModel->devolver((int) $idPrestamo, $_SESSION['usuario_id']);

        if (!$filas) {
            $this->error(400, 'No se encontró un préstamo activo para devolver');
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'message' => 'Préstamo devuelto'
        ], JSON_UNESCAPED_UNICODE);
    }
}
