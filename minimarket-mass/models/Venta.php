<?php
declare(strict_types=1);

/**
 * Venta del Minimarket Mass.
 * Agrupa cliente + productos vendidos con sus cantidades.
 */
class Venta {
    private Cliente $cliente;
    private array $items = [];
    private string $fecha;

    private const TASA_IGV = 0.18;

    public function __construct(Cliente $cliente) {
        $this->cliente = $cliente;
        $this->fecha   = date('Y-m-d H:i:s');
    }

    public function agregarProducto(Producto $producto, int $cantidad): bool {
        if ($cantidad <= 0) {
            throw new InvalidArgumentException("La cantidad debe ser mayor a cero");
        }
        if (!$producto->haySuficienteStock($cantidad)) {
            return false;
        }
        $producto->descontarStock($cantidad);
        $this->items[] = [
            'producto' => $producto,
            'cantidad' => $cantidad
        ];
        return true;
    }

    public function calcularSubtotal(): float {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item['producto']->getPrecio() * $item['cantidad'];
        }
        return round($total, 2);
    }

    public function calcularIGV(): float {
        return round($this->calcularSubtotal() * self::TASA_IGV, 2);
    }

    public function calcularTotal(): float {
        return round($this->calcularSubtotal() + $this->calcularIGV(), 2);
    }

    public function getCliente(): Cliente { return $this->cliente; }
    public function getItems():   array   { return $this->items; }
    public function getFecha():   string  { return $this->fecha; }
}
?>