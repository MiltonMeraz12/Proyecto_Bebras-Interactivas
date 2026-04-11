<?php
namespace App\Enums;

enum TipoInteraccion: string
{
    case SELECCION_SIMPLE = 'seleccion_simple';        // Elegir 1 opción (img o texto)
    case SELECCION_MULTIPLE = 'seleccion_multiple';    // Elegir varias opciones
    case ORDENAR = 'ordenar';                          // Drag & drop para ordenar
    case GRID_SELECCION = 'grid_seleccion';           // Marcar celdas en grid
    case EMPAREJAR = 'emparejar';                      // Unir elementos con líneas
    case RELLENAR = 'rellenar';                        // Colorear/rellenar espacios
    case TEXTO_LIBRE = 'texto_libre';                  // Respuesta escrita
    case SECUENCIA = 'secuencia';                      // Seguir pasos/camino
    case COLOCAR_PIEZAS = 'colocar_piezas';
    case COLOREAR_HEXAGONOS = 'colorear_hexagonos';
    case TEJER_ALFOMBRA = 'tejer_alfombra';
    case COMPLETAR = 'completar';

    public function label(): string
    {
        return match($this) {
            self::SELECCION_SIMPLE   => 'Selección simple',
            self::SELECCION_MULTIPLE => 'Selección múltiple',
            self::ORDENAR            => 'Ordenar',
            self::GRID_SELECCION     => 'Grid de selección',
            self::EMPAREJAR          => 'Emparejar',
            self::RELLENAR           => 'Rellenar',
            self::TEXTO_LIBRE        => 'Texto libre',
            self::SECUENCIA          => 'Secuencia',
            self::COLOCAR_PIEZAS     => 'Colocar piezas',
            self::COLOREAR_HEXAGONOS => 'Colorear hexágonos',
            self::TEJER_ALFOMBRA     => 'Tejer alfombra',
            self::COMPLETAR          => 'Completar',
        };
    }

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}