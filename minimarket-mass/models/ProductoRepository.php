<?php
declare(strict_types=1);
require_once __DIR__ . '/Producto.php';
require_once __DIR__ . '/../config/conexion.php';

class ProductoRepository {

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

    public function crear(array $d): bool {
    try {
        $pdo  = getConexion();
        $stmt = $pdo->prepare(
            "INSERT INTO productos (codigo_barras, nombre, marca, categoria_id, precio, stock)
             VALUES (:codigo, :nombre, :marca, :categoria, :precio, :stock)"
        );
        return $stmt->execute([
            ':codigo' => $d['codigo'], ':nombre' => $d['nombre'], ':marca' => $d['marca'],
            ':categoria' => $d['categoria'], ':precio' => $d['precio'], ':stock' => $d['stock'],
        ]);
    } catch (PDOException $e) {
        error_log('[crear] ' . $e->getMessage());
        return false;
    }
}
}