<?php
declare(strict_types=1);
require_once __DIR__ . '/Producto.php';
require_once __DIR__ . '/../config/conexion.php';

class ProductoRepository {

    // ── Obtener todos los productos ordenados por nombre ──────────────────
    public function obtenerTodos(): array {
        try {
            $pdo = getConexion();
            $stmt = $pdo->query(
                "SELECT codigo_barras AS codigo, nombre, precio, stock
                 FROM productos
                 ORDER BY nombre"
            );
            $productos = [];
            foreach ($stmt->fetchAll() as $f) {
                $productos[] = new Producto(
                    $f['codigo'],
                    $f['nombre'],
                    (float) $f['precio'],
                    (int)   $f['stock']
                );
            }
            return $productos;
        } catch (PDOException $e) {
            error_log('[ProductoRepository::obtenerTodos] ' . $e->getMessage());
            return [];
        }
    }

        // ── Buscar un producto por su código de barras ─────────────────────────
    public function buscarPorCodigo(string $codigo): ?Producto {
        try {
            $pdo = getConexion();
            $stmt = $pdo->prepare(
                "SELECT codigo_barras AS codigo, nombre, precio, stock
                 FROM productos
                 WHERE codigo_barras = :codigo"
            );
            $stmt->execute([':codigo' => $codigo]);
            $fila = $stmt->fetch();
            if ($fila === false) return null;
            return new Producto(
                $fila['codigo'],
                $fila['nombre'],
                (float) $fila['precio'],
                (int)   $fila['stock']
            );
        } catch (PDOException $e) {
            error_log('[ProductoRepository::buscarPorCodigo] ' . $e->getMessage());
            return null;
        }
    }

       // ── Insertar un nuevo producto ─────────────────────────────────────────
    public function crear(array $d): bool {
        try {
            $pdo  = getConexion();
            $stmt = $pdo->prepare(
                "INSERT INTO productos (codigo_barras, nombre, marca, categoria_id, precio, stock)
                 VALUES (:codigo, :nombre, :marca, :categoria, :precio, :stock)"
            );
            return $stmt->execute([
                ':codigo'    => $d['codigo'],
                ':nombre'    => $d['nombre'],
                ':marca'     => $d['marca'],
                ':categoria' => $d['categoria'],
                ':precio'    => $d['precio'],
                ':stock'     => $d['stock'],
            ]);
        } catch (PDOException $e) {
            error_log('[ProductoRepository::crear] ' . $e->getMessage());
            return false;
        }
    }

    // ── Actualizar nombre, precio y stock de un producto existente ─────────
    public function actualizar(Producto $producto): bool {
        try {
            $pdo  = getConexion();
            $stmt = $pdo->prepare(
                "UPDATE productos
                 SET nombre = :nombre, precio = :precio, stock = :stock
                 WHERE codigo_barras = :codigo"
            );
            return $stmt->execute([
                ':nombre' => $producto->getNombre(),
                ':precio' => $producto->getPrecio(),
                ':stock'  => $producto->getStock(),
                ':codigo' => $producto->getCodigo(),
            ]);
        } catch (PDOException $e) {
            error_log('[ProductoRepository::actualizar] ' . $e->getMessage());
            return false;
        }
    }

    // ── Eliminar un producto por código de barras ──────────────────────────
public function eliminar(string $codigo): bool {
    try {
        $pdo  = getConexion();
        $stmt = $pdo->prepare(
            "DELETE FROM productos WHERE codigo_barras = :codigo"
        );
        return $stmt->execute([':codigo' => $codigo]);
    } catch (PDOException $e) {
        error_log('[ProductoRepository::eliminar] ' . $e->getMessage());
        return false;
    }
}
}
?>