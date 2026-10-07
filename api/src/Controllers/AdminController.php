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

    private function error($codigo, $mensaje) {
        http_response_code($codigo);
        echo json_encode([
            'status' => 'error',
            'message' => $mensaje
        ], JSON_UNESCAPED_UNICODE);
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
            $this->error(500, 'No se pudieron leer los artículos');
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'data' => $lista
        ], JSON_UNESCAPED_UNICODE);
    }

    public function usuarios() {
        if (!$this->esAdmin()) {
            return;
        }

        $usuarioModel = new UsuarioModel($this->db);
        $lista = $usuarioModel->listar();

        if ($lista === false) {
            $this->error(500, 'No se pudieron leer los usuarios');
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'data' => $lista
        ], JSON_UNESCAPED_UNICODE);
    }

    public function opciones() {
        if (!$this->esAdmin()) {
            return;
        }

        $articuloModel = new ArticuloModel($this->db);
        $categorias = $articuloModel->listarCategorias();
        $autores = $articuloModel->listarAutores();
        $editoriales = $articuloModel->listarEditoriales();

        if ($categorias === false || $autores === false || $editoriales === false) {
            $this->error(500, 'No se pudieron leer las categorías, autores o editoriales');
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'data' => [
                'categorias' => $categorias,
                'autores' => $autores,
                'editoriales' => $editoriales
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

    public function verArticulo($idArticulo) {
        if (!$this->esAdmin()) {
            return;
        }

        $articuloModel = new ArticuloModel($this->db);
        $articulo = $articuloModel->buscarPorId((int) $idArticulo);

        if (!$articulo) {
            $this->error(404, 'El artículo no existe');
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'data' => $articulo
        ], JSON_UNESCAPED_UNICODE);
    }

    public function crearArticulo() {
        if (!$this->esAdmin()) {
            return;
        }

        $datos = $this->leerArticulo();
        if (!$datos) {
            return;
        }

        $articuloModel = new ArticuloModel($this->db);
        $id = $articuloModel->crear(
            $datos['titulo'],
            $datos['descripcion'],
            $datos['imagen'],
            $datos['anio'],
            $datos['disponible'],
            $datos['id_categoria'],
            $datos['id_autor'],
            $datos['id_editorial']
        );

        if (!$id) {
            $this->error(500, 'No se pudo guardar el artículo');
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'message' => 'Artículo agregado',
            'data' => ['id_articulo' => $id]
        ], JSON_UNESCAPED_UNICODE);
    }

    public function actualizarArticulo($idArticulo) {
        if (!$this->esAdmin()) {
            return;
        }

        $articuloModel = new ArticuloModel($this->db);
        $actual = $articuloModel->buscarPorId((int) $idArticulo);
        if (!$actual) {
            $this->error(404, 'El artículo no existe');
            return;
        }

        $datos = $this->leerArticulo();
        if (!$datos) {
            return;
        }

        $imagen = $datos['imagen'] ?? $actual['imagen'];

        $ok = $articuloModel->actualizar(
            (int) $idArticulo,
            $datos['titulo'],
            $datos['descripcion'],
            $imagen,
            $datos['anio'],
            $datos['disponible'],
            $datos['id_categoria'],
            $datos['id_autor'],
            $datos['id_editorial']
        );

        if (!$ok) {
            $this->error(500, 'No se pudo modificar el artículo');
            return;
        }

        if ($datos['imagen'] !== null) {
            $this->borrarImagen($actual['imagen']);
        }

        echo json_encode([
            'status' => 'ok',
            'message' => 'Artículo modificado'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function eliminarArticulo($idArticulo) {
        if (!$this->esAdmin()) {
            return;
        }

        $articuloModel = new ArticuloModel($this->db);
        $actual = $articuloModel->buscarPorId((int) $idArticulo);
        if (!$actual) {
            $this->error(404, 'El artículo no existe');
            return;
        }

        if (!$articuloModel->eliminar((int) $idArticulo)) {
            $this->error(500, 'No se pudo eliminar el artículo');
            return;
        }

        $this->borrarImagen($actual['imagen']);

        echo json_encode([
            'status' => 'ok',
            'message' => 'Artículo eliminado'
        ], JSON_UNESCAPED_UNICODE);
    }

    private function leerArticulo() {
        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos)) {
            $datos = [];
        }

        $titulo = trim(strip_tags((string) ($datos['titulo'] ?? '')));
        $descripcion = trim(strip_tags((string) ($datos['descripcion'] ?? '')));
        $nombreAutor = trim(strip_tags((string) ($datos['autor'] ?? '')));
        $idCategoria = (int) ($datos['id_categoria'] ?? 0);
        $idEditorial = (int) ($datos['id_editorial'] ?? 0);
        $anio = (int) ($datos['anio_publicacion'] ?? 0);
        $disponible = !empty($datos['disponible']) ? 1 : 0;
        $imagenBase64 = (string) ($datos['imagen'] ?? '');

        if ($titulo === '' || $nombreAutor === '' || $idCategoria <= 0) {
            $this->error(400, 'Título, autor y categoría son obligatorios');
            return false;
        }

        if ($anio !== 0 && ($anio < 1000 || $anio > (int) date('Y'))) {
            $this->error(400, 'El año de publicación no es válido');
            return false;
        }

        $imagen = null;
        if ($imagenBase64 !== '') {
            $imagen = $this->guardarImagen($imagenBase64);
            if ($imagen === false) {
                $this->error(400, 'La imagen tiene que ser JPG, PNG o WEBP y pesar menos de 2 MB');
                return false;
            }
        }

        $articuloModel = new ArticuloModel($this->db);
        $idAutor = $articuloModel->buscarOCrearAutor($nombreAutor);
        if (!$idAutor) {
            $this->error(500, 'No se pudo guardar el autor');
            return false;
        }

        return [
            'titulo' => $titulo,
            'descripcion' => $descripcion !== '' ? $descripcion : null,
            'imagen' => $imagen,
            'anio' => $anio !== 0 ? $anio : null,
            'disponible' => $disponible,
            'id_categoria' => $idCategoria,
            'id_autor' => $idAutor,
            'id_editorial' => $idEditorial > 0 ? $idEditorial : null
        ];
    }

    private function guardarImagen($imagenBase64) {
        if (!preg_match('#^data:image/(jpeg|png|webp);base64,#', $imagenBase64, $tipo)) {
            return false;
        }

        $contenido = base64_decode(substr($imagenBase64, strpos($imagenBase64, ',') + 1), true);
        if ($contenido === false || strlen($contenido) > 2 * 1024 * 1024) {
            return false;
        }

        $extension = $tipo[1] === 'jpeg' ? 'jpg' : $tipo[1];
        $carpeta = dirname(__DIR__, 3) . '/front/dist/assets/imagenes';

        if (!is_dir($carpeta) && !mkdir($carpeta, 0775, true)) {
            return false;
        }

        $nombreArchivo = 'articulo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        if (file_put_contents($carpeta . '/' . $nombreArchivo, $contenido) === false) {
            return false;
        }

        return 'imagenes/' . $nombreArchivo;
    }

    private function borrarImagen($imagen) {
        if (!$imagen || strpos($imagen, 'imagenes/') !== 0) {
            return;
        }

        $ruta = dirname(__DIR__, 3) . '/front/dist/assets/imagenes/' . basename($imagen);
        if (is_file($ruta)) {
            unlink($ruta);
        }
    }
}
