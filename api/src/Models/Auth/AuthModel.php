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
}
