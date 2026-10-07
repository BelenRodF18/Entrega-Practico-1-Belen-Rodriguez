<?php
namespace App\Models;

class PrestamoModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function listarPorUsuario($idUsuario) {
        try {
            $stmt = $this->db->prepare(
                'SELECT p.id_prestamo, p.id_articulo, p.fecha_prestamo, p.fecha_devolucion, p.estado,
                        a.titulo, a.imagen, c.nombre_categoria, c.accion, au.nombre_autor
                 FROM PRESTAMOS p
                 INNER JOIN ARTICULOS a ON a.id_articulo = p.id_articulo
                 INNER JOIN CATEGORIAS c ON c.id_categoria = a.id_categoria
                 INNER JOIN AUTORES au ON au.id_autor = a.id_autor
                 WHERE p.id_usuario = :id
                 ORDER BY p.estado, p.fecha_prestamo DESC'
            );
            $stmt->execute(['id' => $idUsuario]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function tieneActivo($idUsuario, $idArticulo) {
        try {
            $stmt = $this->db->prepare(
                'SELECT COUNT(*) AS total FROM PRESTAMOS
                 WHERE id_usuario = :id_usuario AND id_articulo = :id_articulo AND estado = \'Activo\''
            );
            $stmt->execute(['id_usuario' => $idUsuario, 'id_articulo' => $idArticulo]);
            $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $fila['total'] > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function crear($idUsuario, $idArticulo) {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO PRESTAMOS (id_usuario, id_articulo, fecha_prestamo, estado)
                 VALUES (:id_usuario, :id_articulo, CURDATE(), \'Activo\')'
            );
            $stmt->execute(['id_usuario' => $idUsuario, 'id_articulo' => $idArticulo]);
            return (int) $this->db->lastInsertId();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function devolver($idPrestamo, $idUsuario) {
        try {
            $stmt = $this->db->prepare(
                'UPDATE PRESTAMOS SET estado = \'Devuelto\', fecha_devolucion = CURDATE()
                 WHERE id_prestamo = :id_prestamo AND id_usuario = :id_usuario AND estado = \'Activo\''
            );
            $stmt->execute(['id_prestamo' => $idPrestamo, 'id_usuario' => $idUsuario]);
            return $stmt->rowCount();
        } catch (\Exception $e) {
            return false;
        }
    }
}
