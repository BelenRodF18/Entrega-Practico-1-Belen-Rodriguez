<?php
namespace App\Controllers;

use App\Models\ArticuloModel;

class EstadoController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function ver() {
        $respuesta = [
            'status' => 'ok',
            'message' => 'La API responde',
            'base_datos' => 'conectada',
            'tabla_articulos' => 'no existe todavía',
            'cantidad_articulos' => 0
        ];

        $articuloModel = new ArticuloModel($this->db);

        $cantidad = $articuloModel->contar();

        if ($cantidad !== false) {
            $respuesta['tabla_articulos'] = 'ok';
            $respuesta['cantidad_articulos'] = $cantidad;
        } else {
            $respuesta['tabla_articulos'] = 'Falta crear la tabla ARTICULOS (phpMyAdmin o init.sql)';
        }

        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    }
}
