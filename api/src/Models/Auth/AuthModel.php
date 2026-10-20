<?php
namespace App\Models\Auth;

class AuthModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function buscarPorEmail($email) {
        try {
            $stmt = $this->db->prepare(
                'SELECT u.id_usuario, u.nombre, u.email, u.password_hash, r.nombre_rol
                 FROM USUARIOS u
                 INNER JOIN ROLES r ON r.id_rol = u.id_rol
                 WHERE u.email = :email
                 LIMIT 1'
            );
            $stmt->execute(['email' => $email]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function crearUsuario($nombre, $email, $passwordHash, $idCategoria) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO USUARIOS (nombre, email, password_hash, id_rol, id_categoria_interes)
                 SELECT :nombre, :email, :hash, id_rol, :categoria
                 FROM ROLES
                 WHERE nombre_rol = 'Usuario'"
            );
            $stmt->execute([
                'nombre' => $nombre,
                'email' => $email,
                'hash' => $passwordHash,
                'categoria' => $idCategoria
            ]);
            return $stmt->rowCount() === 1;
        } catch (\Exception $e) {
            return false;
        }
    }
}
