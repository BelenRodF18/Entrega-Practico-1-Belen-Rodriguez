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
}
