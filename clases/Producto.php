<?php
declare(strict_types=1);

class Producto {

    private string $nombre;
    private string $categoria;
    private float  $precio;
    private int    $cantidad;
    private float  $tasaIgv;

    // 1. Centralizamos todas las categorías permitidas
    private const CATEGORIAS_PERMITIDAS = [
        'panaderia', 
        'frutas_verduras', 
        'bebidas', 
        'lacteos', 
        'abarrotes'
    ];

    // Categorías exoneradas de IGV (subset de las permitidas)
    private const CATEGORIAS_SIN_IGV = ['panaderia', 'frutas_verduras'];
    private const TASA_IGV_NORMAL    = 0.18;

    public function __construct(
        string $nombre,
        string $categoria,
        float  $precio,
        int    $cantidad
    ) {
        // Validaciones existentes
        if (empty($nombre)) {
            throw new InvalidArgumentException("El nombre del producto no puede estar vacío.");
        }
        if ($precio < 0) {
            throw new InvalidArgumentException("El precio no puede ser negativo.");
        }
        if ($cantidad < 0) {
            throw new InvalidArgumentException("La cantidad no puede ser negativa.");
        }

        // 2. Nueva validación de categoría
        if (!in_array($categoria, self::CATEGORIAS_PERMITIDAS, true)) {
            throw new InvalidArgumentException("Categoría inválida: {$categoria}.");
        }

        $this->nombre    = $nombre;
        $this->categoria = $categoria;
        $this->precio    = $precio;
        $this->cantidad  = $cantidad;

        // 3. Determinación de tasa basada en la constante
        $this->tasaIgv = in_array($categoria, self::CATEGORIAS_SIN_IGV, true)
            ? 0.00
            : self::TASA_IGV_NORMAL;
    }

    // --- (Resto de métodos: getters y cálculos se mantienen igual) ---
    public function getNombre(): string    { return $this->nombre; }
    public function getCategoria(): string { return $this->categoria; }
    public function getPrecio(): float     { return $this->precio; }
    public function getCantidad(): int     { return $this->cantidad; }
    public function getTasaIgv(): float    { return $this->tasaIgv; }

    public function calcularTotal(): float { return $this->precio * $this->cantidad; }

    public function calcularNeto(): float {
        return ($this->tasaIgv > 0) ? ($this->calcularTotal() / (1 + $this->tasaIgv)) : $this->calcularTotal();
    }

    public function calcularIgv(): float { return $this->calcularTotal() - $this->calcularNeto(); }
    public function tieneIgv(): bool { return $this->tasaIgv > 0; }
}