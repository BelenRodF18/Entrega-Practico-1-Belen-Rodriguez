<?php
namespace App\Controllers;

use App\Models\ArticuloModel;

class ArticuloController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function listar() {
        $respuesta = [
            'status' => 'ok',
            'message' => 'Catálogo',
            'data' => []
        ];

        $articuloModel = new ArticuloModel($this->db);
        $articulos = $articuloModel->listar();

        if ($articulos !== false) {
            $respuesta['data'] = $articulos;
        } else {
            http_response_code(500);
            $respuesta['status'] = 'error';
            $respuesta['message'] = 'No se pudo consultar el catálogo';
        }

        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    }
}
