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
}