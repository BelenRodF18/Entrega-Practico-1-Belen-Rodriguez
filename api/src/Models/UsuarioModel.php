<?php
namespace App\Models;

class UsuarioModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function contarPorRol($nombreRol) {
        try {
            $stmt = $this->db->prepare(
                'SELECT COUNT(*) AS total
                 FROM USUARIOS u
                 INNER JOIN ROLES r ON r.id_rol = u.id_rol
                 WHERE r.nombre_rol = :rol'
            );
            $stmt->execute(['rol' => $nombreRol]);
            $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $fila['total'];
        } catch (\Exception $e) {
            return false;
        }
    }

    public function listar() {
        try {
            $stmt = $this->db->query(
                'SELECT u.id_usuario, u.nombre, u.email, u.fecha_registro, r.nombre_rol, c.nombre_categoria,
                        (SELECT COUNT(*) FROM PRESTAMOS p
                         WHERE p.id_usuario = u.id_usuario AND p.estado = \'Activo\') AS prestamos_activos
                 FROM USUARIOS u
                 INNER JOIN ROLES r ON r.id_rol = u.id_rol
                 LEFT JOIN CATEGORIAS c ON c.id_categoria = u.id_categoria_interes
                 ORDER BY u.id_usuario'
            );
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function buscarPorId($idUsuario) {
        try {
            $stmt = $this->db->prepare(
                'SELECT u.nombre, u.email, u.fecha_registro, r.nombre_rol, c.nombre_categoria
                 FROM USUARIOS u
                 INNER JOIN ROLES r ON r.id_rol = u.id_rol
                 LEFT JOIN CATEGORIAS c ON c.id_categoria = u.id_categoria_interes
                 WHERE u.id_usuario = :id
                 LIMIT 1'
            );
            $stmt->execute(['id' => $idUsuario]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }
}
