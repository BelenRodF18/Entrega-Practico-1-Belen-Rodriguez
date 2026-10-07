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
                'SELECT a.id_articulo, a.titulo, a.descripcion, a.imagen, a.anio_publicacion, a.disponible,
                        a.id_categoria, a.id_editorial, c.nombre_categoria, c.accion, au.nombre_autor, e.nombre_editorial
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

    public function buscarPorId($idArticulo) {
        try {
            $stmt = $this->db->prepare(
                'SELECT a.id_articulo, a.titulo, a.descripcion, a.imagen, a.anio_publicacion, a.disponible,
                        a.id_categoria, a.id_editorial, au.nombre_autor
                 FROM ARTICULOS a
                 INNER JOIN AUTORES au ON au.id_autor = a.id_autor
                 WHERE a.id_articulo = :id
                 LIMIT 1'
            );
            $stmt->execute(['id' => $idArticulo]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function buscarDisponible($idArticulo) {
        try {
            $stmt = $this->db->prepare(
                'SELECT id_articulo, titulo FROM ARTICULOS
                 WHERE id_articulo = :id AND disponible = 1
                 LIMIT 1'
            );
            $stmt->execute(['id' => $idArticulo]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function listarCategorias() {
        try {
            $stmt = $this->db->query('SELECT id_categoria, nombre_categoria FROM CATEGORIAS ORDER BY nombre_categoria');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function listarAutores() {
        try {
            $stmt = $this->db->query('SELECT id_autor, nombre_autor FROM AUTORES ORDER BY nombre_autor');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function listarEditoriales() {
        try {
            $stmt = $this->db->query('SELECT id_editorial, nombre_editorial FROM EDITORIALES ORDER BY nombre_editorial');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function buscarOCrearAutor($nombreAutor) {
        try {
            $stmt = $this->db->prepare('SELECT id_autor FROM AUTORES WHERE nombre_autor = :nombre LIMIT 1');
            $stmt->execute(['nombre' => $nombreAutor]);
            $fila = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($fila) {
                return (int) $fila['id_autor'];
            }

            $stmt = $this->db->prepare('INSERT INTO AUTORES (nombre_autor) VALUES (:nombre)');
            $stmt->execute(['nombre' => $nombreAutor]);
            return (int) $this->db->lastInsertId();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function crear($titulo, $descripcion, $imagen, $anio, $disponible, $idCategoria, $idAutor, $idEditorial) {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO ARTICULOS (titulo, descripcion, imagen, anio_publicacion, disponible, id_categoria, id_autor, id_editorial)
                 VALUES (:titulo, :descripcion, :imagen, :anio, :disponible, :id_categoria, :id_autor, :id_editorial)'
            );
            $stmt->execute([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'imagen' => $imagen,
                'anio' => $anio,
                'disponible' => $disponible,
                'id_categoria' => $idCategoria,
                'id_autor' => $idAutor,
                'id_editorial' => $idEditorial
            ]);
            return (int) $this->db->lastInsertId();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actualizar($idArticulo, $titulo, $descripcion, $imagen, $anio, $disponible, $idCategoria, $idAutor, $idEditorial) {
        try {
            $stmt = $this->db->prepare(
                'UPDATE ARTICULOS
                 SET titulo = :titulo, descripcion = :descripcion, imagen = :imagen, anio_publicacion = :anio,
                     disponible = :disponible, id_categoria = :id_categoria, id_autor = :id_autor, id_editorial = :id_editorial
                 WHERE id_articulo = :id'
            );
            return $stmt->execute([
                'id' => $idArticulo,
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'imagen' => $imagen,
                'anio' => $anio,
                'disponible' => $disponible,
                'id_categoria' => $idCategoria,
                'id_autor' => $idAutor,
                'id_editorial' => $idEditorial
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function eliminar($idArticulo) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare('DELETE FROM PRESTAMOS WHERE id_articulo = :id');
            $stmt->execute(['id' => $idArticulo]);

            $stmt = $this->db->prepare('DELETE FROM ARTICULOS WHERE id_articulo = :id');
            $stmt->execute(['id' => $idArticulo]);

            $this->db->commit();
            return $stmt->rowCount();
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }
}
