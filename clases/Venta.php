<?php
declare(strict_types=1);

require_once __DIR__ . '/../clases/Producto.php';
require_once __DIR__ . '/../clases/Cliente.php';
require_once __DIR__ . '/../clases/Venta.php';

class Venta {

    private Cliente $cliente;
    private string  $metodoPago;
    private array   $productos = [];   // array de Producto

    private const DESCUENTOS_MONTO = [
        200 => 0.15,
        100 => 0.10,
        30  => 0.05,
    ];

    private const INSTRUCCIONES_PAGO = [
        'Efectivo' => 'Pago en efectivo — exacto preferido',
        'Yape'     => 'Mostrar QR del comercio',
        'Plin'     => 'Mostrar QR del comercio',
        'Tarjeta'  => 'Insertar tarjeta en POS',
    ];

    public function __construct(Cliente $cliente, string $metodoPago = 'Efectivo') {
        if (!array_key_exists($metodoPago, self::INSTRUCCIONES_PAGO)) {
            throw new InvalidArgumentException("Método de pago inválido: $metodoPago");
        }
        $this->cliente    = $cliente;
        $this->metodoPago = $metodoPago;
    }

    // ── Gestión de productos ─────────────────────────────────
    public function agregarProducto(Producto $producto): void {
        $this->productos[] = $producto;
    }

    public function getProductos(): array   { return $this->productos; }
    public function getCliente(): Cliente   { return $this->cliente; }
    public function getMetodoPago(): string { return $this->metodoPago; }

    // ── Totales de productos ─────────────────────────────────
    public function calcularTotalProductos(): float {
        return array_sum(array_map(fn(Producto $p) => $p->calcularTotal(), $this->productos));
    }

    public function calcularTotalNeto(): float {
        return array_sum(array_map(fn(Producto $p) => $p->calcularNeto(), $this->productos));
    }

    public function calcularTotalIgv(): float {
        return array_sum(array_map(fn(Producto $p) => $p->calcularIgv(), $this->productos));
    }

    // ── Descuentos ───────────────────────────────────────────

    /** Porcentaje de descuento según monto total */
    public function getPorcentajeDescuentoMonto(): float {
        $total = $this->calcularTotalProductos();
        foreach (self::DESCUENTOS_MONTO as $minimo => $porcentaje) {
            if ($total >= $minimo) {
                return $porcentaje;
            }
        }
        return 0.00;
    }

    public function calcularDescuentoMonto(): float {
        return $this->calcularTotalProductos() * $this->getPorcentajeDescuentoMonto();
    }

    public function calcularDescuentoCliente(): float {
        return $this->calcularTotalProductos() * $this->cliente->getPorcentajeDescuento();
    }

    public function calcularTotalDescuentos(): float {
        return $this->calcularDescuentoMonto() + $this->calcularDescuentoCliente();
    }

    public function calcularPorcentajeTotalDescuento(): float {
        return ($this->getPorcentajeDescuentoMonto() + $this->cliente->getPorcentajeDescuento()) * 100;
    }

    // ── Total final ──────────────────────────────────────────
    public function calcularTotalAPagar(): float {
        return $this->calcularTotalProductos() - $this->calcularTotalDescuentos();
    }

    // ── Método de pago ───────────────────────────────────────
    public function getInstruccionPago(): string {
        return self::INSTRUCCIONES_PAGO[$this->metodoPago];
    }
}