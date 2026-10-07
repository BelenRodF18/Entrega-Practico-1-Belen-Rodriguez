<?php
namespace App\Models;

class ArticuloModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function contar() {
        try {
            $stmt = $this->db->query('SELECT COUNT(*) AS total FROM ARTICULOS');
            $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $fila['total'];
        } catch (\Exception $e) {
            return false;
        }
    }

    public function contarDisponibles() {
        try {
            $stmt = $this->db->query('SELECT COUNT(*) AS total FROM ARTICULOS WHERE disponible = 1');
            $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $fila['total'];
        } catch (\Exception $e) {
            return false;
        }
    }

    public function listar() {
        try {
            $stmt = $this->db->query(
                'SELECT a.id_articulo, a.titulo, a.imagen, a.anio_publicacion, a.disponible,
                        c.nombre_categoria, au.nombre_autor, e.nombre_editorial
                 FROM ARTICULOS a
                 INNER JOIN CATEGORIAS c ON c.id_categoria = a.id_categoria
                 INNER JOIN AUTORES au ON au.id_autor = a.id_autor
                 LEFT JOIN EDITORIALES e ON e.id_editorial = a.id_editorial
                 ORDER BY a.id_articulo'
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }
}
