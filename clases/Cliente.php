<?php
declare(strict_types=1);

class Cliente {

    private string $nombre;
    private string $dni;
    private string $tipo;   // Regular | Frecuente | VIP

    private const DESCUENTOS = [
        'Regular'   => 0.00,
        'Frecuente' => 0.02,
        'VIP'       => 0.05,
    ];

    public function __construct(string $nombre, string $dni, string $tipo = 'Regular') {
        if (strlen($dni) !== 8 || !ctype_digit($dni)) {
            throw new InvalidArgumentException("El DNI debe tener exactamente 8 dígitos numéricos.");
        }
        if (!array_key_exists($tipo, self::DESCUENTOS)) {
            throw new InvalidArgumentException("Tipo de cliente inválido: $tipo");
        }

        $this->nombre = $nombre;
        $this->dni    = $dni;
        $this->tipo   = $tipo;
    }

    // ── Getters ──────────────────────────────────────────────
    public function getNombre(): string { return $this->nombre; }
    public function getDni(): string    { return $this->dni; }
    public function getTipo(): string   { return $this->tipo; }

    /** Porcentaje de descuento según tipo (0.00, 0.02 o 0.05) */
    public function getPorcentajeDescuento(): float {
        return self::DESCUENTOS[$this->tipo];
    }

    /** Saludo según hora del día */
    public function saludo(): string {
        $hora = (int) date("H");
        if ($hora < 12)       $saludo = "Buenos días";
        elseif ($hora < 19)   $saludo = "Buenas tardes";
        else                  $saludo = "Buenas noches";

        return "$saludo, {$this->nombre}!";
    }
}