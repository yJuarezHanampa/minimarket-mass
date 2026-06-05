<?php
declare(strict_types=1);

/**
 * Cliente del Minimarket Mass.
 * Identificado por DNI peruano (8 dígitos exactos).
 */
class Cliente {
    private string $dni;
    private string $nombre;
    private string $apellido;

    public function __construct(string $dni, string $nombre, string $apellido) {
        if (!preg_match('/^\d{8}$/', $dni)) {
            throw new InvalidArgumentException("DNI inválido: debe tener 8 dígitos");
        }
        $this->dni      = $dni;
        $this->nombre   = $nombre;
        $this->apellido = $apellido;
    }

    public function getDni():      string { return $this->dni; }
    public function getNombre():   string { return $this->nombre; }
    public function getApellido(): string { return $this->apellido; }

    public function nombreCompleto(): string {
        return $this->nombre . " " . $this->apellido;
    }
}
?>