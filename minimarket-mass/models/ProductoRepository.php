<?php
declare(strict_types=1);

require_once __DIR__ . '/Producto.php';
require_once __DIR__ . '/../config/conexion.php';

/**
 * Repositorio de productos del Minimarket Mass.
 *
 * SESIÓN 4: usaba un array hardcoded.
 * SESIÓN 5: ahora lee de MySQL con PDO.
 *
 * El cambio es INTERNO: los métodos siguen devolviendo lo mismo
 * (array de Producto o ?Producto). Por eso el Controller y la View
 * NO se tocan. Ese es el payoff del MVC.
 */
class ProductoRepository {

    /**
     * Devuelve TODOS los productos del catálogo desde la BD.
     * @return Producto[]
     */
    public function obtenerTodos(): array {
        try {
            $pdo = getConexion();

            // codigo_barras AS codigo → la columna real es codigo_barras,
            // pero la clase Producto espera "codigo". El alias los empata.
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
                    (float) $f['precio'],   // MySQL devuelve TODO como string
                    (int)   $f['stock']     // por eso casteamos a float/int
                );
            }
            return $productos;

        } catch (PDOException $e) {
            error_log('[ProductoRepository::obtenerTodos] ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca UN producto por su código.
     * Usa PREPARED STATEMENT → seguro contra SQL injection.
     */
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
            if ($fila === false) {
                return null;
            }

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

    /**
     * 1. CORREGIDO: Devuelve todos los productos cuyo nombre contenga el texto buscado.
     * Usa LIKE con un prepared statement de forma segura.
     * @return Producto[]
     */
    public function buscarPorNombre(string $termino): array {
        try {
            $pdo = getConexion();

            $stmt = $pdo->prepare(
                "SELECT codigo_barras AS codigo, nombre, precio, stock
                 FROM productos
                 WHERE nombre LIKE :termino
                 ORDER BY nombre"
            );
            
            $stmt->execute([':termino' => '%' . $termino . '%']);

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
            error_log('[ProductoRepository::buscarPorNombre] ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 2. Devuelve los productos de una categoría.
     * Filtra por categoria_id con un parámetro :id (prepared statement).
     * @return Producto[]
     */
    public function obtenerPorCategoria(int $categoriaId): array {
        try {
            $pdo = getConexion();

            $stmt = $pdo->prepare(
                "SELECT codigo_barras AS codigo, nombre, precio, stock
                 FROM productos
                 WHERE categoria_id = :id
                 ORDER BY nombre"
            );
            
            $stmt->execute([':id' => $categoriaId]);

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
            error_log('[ProductoRepository::obtenerPorCategoria] ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 3. Devuelve los productos cuyo stock esté por debajo del umbral indicado,
     * ordenados de menor a mayor stock (los más urgentes primero).
     * @return Producto[]
     */
    public function obtenerBajoStock(int $umbral): array {
        try {
            $pdo = getConexion();

            $stmt = $pdo->prepare(
                "SELECT codigo_barras AS codigo, nombre, precio, stock
                 FROM productos
                 WHERE stock < :umbral
                 ORDER BY stock ASC"
            );
            
            $stmt->execute([':umbral' => $umbral]);

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
            error_log('[ProductoRepository::obtenerBajoStock] ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 4. Devuelve un número entero con la cantidad total de productos en la tabla.
     * Usa COUNT(*) y fetchColumn().
     */
    public function contarTotalProductos(): int {
        try {
            $pdo = getConexion();

            $stmt = $pdo->query("SELECT COUNT(*) FROM productos");
            
            return (int) $stmt->fetchColumn();

        } catch (PDOException $e) {
            error_log('[ProductoRepository::contarTotalProductos] ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * 5. Devuelve los N productos con el precio más alto del catálogo.
     * Sirve para identificar el inventario de mayor valor o productos premium.
     * @return Producto[]
     */
    public function obtenerMasCaros(int $limite): array {
        try {
            $pdo = getConexion();

            $stmt = $pdo->prepare(
                "SELECT codigo_barras AS codigo, nombre, precio, stock
                 FROM productos
                 ORDER BY precio DESC
                 LIMIT :limite"
            );
            
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();

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
            error_log('[ProductoRepository::obtenerMasCaros] ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 6. Devuelve todos los productos que pertenecen a una marca específica.
     * Sirve para agrupar y mostrar el inventario por su fabricante o proveedor.
     * @return Producto[]
     */
    public function buscarPorMarca(string $marca): array {
        try {
            $pdo = getConexion();

            $stmt = $pdo->prepare(
                "SELECT codigo_barras AS codigo, nombre, precio, stock
                 FROM productos
                 WHERE marca = :marca
                 ORDER BY nombre"
            );
            
            $stmt->execute([':marca' => $marca]);

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
            error_log('[ProductoRepository::buscarPorMarca] ' . $e->getMessage());
            return [];
        }
    }
}