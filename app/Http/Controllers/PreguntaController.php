<?php

namespace App\Http\Controllers;

use App\Models\Conjunto;
use App\Models\Pregunta;
use App\Models\ProgresoUsuario;
use App\Models\SesionConjunto;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class PreguntaController extends Controller
{
    public function show(Conjunto $conjunto, Pregunta $pregunta): View|RedirectResponse
    {
        // Verificar que la pregunta pertenece al conjunto
        abort_if($pregunta->conjunto_id !== $conjunto->id, 404);
        abort_if(!$pregunta->activa && auth()->user()->isAlumno(), 403);

        // El alumno debe tener sesión activa
        $sesion = SesionConjunto::where('user_id', auth()->id())
            ->where('conjunto_id', $conjunto->id)
            ->first();

        if (!$sesion && auth()->user()->isAlumno()) {
            return redirect()->route('conjuntos.show', $conjunto)
                ->with('error', 'Debes iniciar el conjunto antes de responder.');
        }

        // Si ya terminó, redirigir a resultados
        if ($sesion?->estaTerminada() && auth()->user()->isAlumno()) {
            return redirect()->route('conjuntos.resultados', $conjunto);
        }

        $progreso = ProgresoUsuario::where('user_id', auth()->id())
            ->where('pregunta_id', $pregunta->id)
            ->first();

        // Navegación: pregunta anterior y siguiente
        $preguntas  = $conjunto->preguntas()->activas()->orderBy('orden')->pluck('id');
        $posicion   = $preguntas->search($pregunta->id);
        $anteriorId = $posicion > 0 ? $preguntas[$posicion - 1] : null;
        $siguienteId= $posicion < $preguntas->count() - 1 ? $preguntas[$posicion + 1] : null;

        $anterior = $anteriorId ? Pregunta::find($anteriorId) : null;
        $siguiente= $siguienteId ? Pregunta::find($siguienteId) : null;
        $progresoUsuario = $progreso;

        return view('preguntas.show', compact(
            'conjunto', 'pregunta', 'progreso', 'anterior', 'siguiente', 'preguntas', 'posicion', 'progresoUsuario', 'sesion'
        ));
    }

    public function verificar(Request $request, Conjunto $conjunto, Pregunta $pregunta): JsonResponse
    {
        abort_if($pregunta->conjunto_id !== $conjunto->id, 404);

        // Verificar sesión activa
        $sesion = SesionConjunto::where('user_id', auth()->id())
            ->where('conjunto_id', $conjunto->id)
            ->firstOrFail();

        if ($sesion->estaTerminada()) {
            return response()->json([
                'error'   => true,
                'mensaje' => 'Este conjunto ya fue finalizado.',
            ], 403);
        }

        // Evitar doble envío
        $yaRespondio = ProgresoUsuario::where('user_id', auth()->id())
            ->where('pregunta_id', $pregunta->id)
            ->exists();

        if ($yaRespondio) {
            return response()->json([
                'error'   => true,
                'mensaje' => 'Ya respondiste esta pregunta.',
            ], 422);
        }

        $request->validate([
            'respuesta' => ['present'],
        ]);

        $esCorrecta = $this->validarRespuesta(
            $pregunta->tipo_interaccion,
            $request->input('respuesta'),
            $pregunta->respuesta_correcta,
            $pregunta->configuracion ?? []
        );

        ProgresoUsuario::create([
            'user_id'          => auth()->id(),
            'pregunta_id'      => $pregunta->id,
            'respuesta_usuario'=> $request->input('respuesta'),
            'es_correcta'      => $esCorrecta,
            'completada_at'    => now(),
        ]);

        return response()->json([
            'correcta'                 => $esCorrecta,
            'explicacion'              => $pregunta->explicacion,
            'respuesta_correcta_visual'=> $this->formatearRespuestaCorrecta(
                                             $pregunta->tipo_interaccion,
                                             $pregunta->respuesta_correcta
                                         ),
            'imagen_explicacion'       => $pregunta->imagen_explicacion,
        ]);
    }

    // Validaciones
    private function validarRespuesta($tipo, $usuario, $correcta, $configuracion = []): bool
    {
        if (!$usuario || empty($tipo)) {
            return false;
        }

        switch ($tipo) {
            case 'seleccion_simple':
                return $this->validarSeleccionSimple($usuario, $correcta);
            
            case 'seleccion_multiple':
                return $this->validarSeleccionMultiple($usuario, $correcta);
            
            case 'ordenar':
                return $this->validarOrdenar($usuario, $correcta);
            
            case 'grid_seleccion':
                return $this->validarGrid($usuario, $correcta);
            
            case 'emparejar':
                return $this->validarEmparejar($usuario, $correcta);
            
            case 'rellenar':
                return $this->validarRellenar($usuario, $correcta, $configuracion);
            
            case 'texto_libre':
                return $this->validarTextoLibre($usuario, $correcta);
            
            case 'colocar_piezas':
                return $this->validarColocarPiezas($usuario, $correcta);
            
            case 'rompecabezas_hexagonos':
                return $this->validarRompecabezasHexagonos($usuario, $correcta);
            
            case 'colorear_hexagonos':
                return $this->validarColorearHexagonos($usuario, $correcta);
            
            case 'tejer_alfombra':
                return $this->validarTejerAlfombra($usuario, $correcta);

            case 'completar':
                return $this->validarCompletar($usuario, $correcta, $configuracion);

            default:
                return false;
        }
    }

    private function validarSeleccionSimple($usuario, $correcta): bool
    {
        // $correcta puede ser ['B'] o [['B']]
        $correctaFlat = is_array($correcta[0] ?? null) ? $correcta[0] : $correcta;
        return isset($usuario[0]) && in_array($usuario[0], $correctaFlat);
    }

    private function validarSeleccionMultiple($usuario, $correcta): bool
    {
        // Verificar si es formato de computadoras (pregunta 23)
        if (isset($correcta[0]) && is_array($correcta[0]) && isset($correcta[0]['computadora'])) {
            // Formato: [{computadora: 1, estado: 'virus_rojo'}, ...]
            if (!is_array($usuario) || count($usuario) !== count($correcta)) {
                return false;
            }
            
            // Verificar que cada computadora tenga el estado correcto
            foreach ($correcta as $respuestaCorrecta) {
                $encontrado = false;
                foreach ($usuario as $respuestaUsuario) {
                    if (isset($respuestaUsuario['computadora']) && isset($respuestaUsuario['estado']) &&
                        isset($respuestaCorrecta['computadora']) && isset($respuestaCorrecta['estado'])) {
                        
                        if ((int)$respuestaUsuario['computadora'] === (int)$respuestaCorrecta['computadora'] &&
                            (string)$respuestaUsuario['estado'] === (string)$respuestaCorrecta['estado']) {
                            $encontrado = true;
                            break;
                        }
                    }
                }
                if (!$encontrado) {
                    return false;
                }
            }
            return true;
        }
        
        // Formato estándar: $correcta puede ser [['1', '2', '3']] o ['1', '2', '3']
        $correctaFlat = is_array($correcta[0] ?? null) && !is_string($correcta[0] ?? null)
            ? ($correcta[0] ?? [])
            : $correcta;
        
        if (count($usuario) !== count($correctaFlat)) {
            return false;
        }
        
        $usuarioSorted  = $usuario;
        $correctaSorted = $correctaFlat;
        sort($usuario);
        sort($correctaFlat);
        
        return $usuario === $correctaFlat;
    }

    private function validarOrdenar($usuario, $correcta): bool
    {
        // Verificar si hay múltiples respuestas correctas posibles
        $soluciones = isset($correcta[0]) && is_array($correcta[0]) ? $correcta : [$correcta];
        foreach ($soluciones as $solucion) {
            if ($usuario === $solucion) return true;
        }
        return false;
    }

    private function validarGrid($usuario, $correcta): bool
    {
        if (!is_array($usuario) || count($usuario) !== count($correcta)) return false;

        foreach ($correcta as $celdaCorrecta) {
            $encontrada = false;
            foreach ($usuario as $celdaUsuario) {
                if (($celdaUsuario['fila'] ?? null) == ($celdaCorrecta['fila'] ?? null)
                    && ($celdaUsuario['columna'] ?? null) == ($celdaCorrecta['columna'] ?? null)) {
                    $encontrada = true;
                    break;
                }
            }
            if (!$encontrada) return false;
        }
        return true;
    }

    private function validarEmparejar($usuario, $correcta): bool
    {
        if (!is_array($usuario) || !is_array($correcta)) {
            return false;
        }

        if (count($usuario) !== count($correcta)) {
            return false;
        }
        
        foreach ($correcta as $parCorrecto) {
            $encontrado = false;
            foreach ($usuario as $parUsuario) {
                if (($parUsuario['objeto'] ?? null) == ($parCorrecto['objeto'] ?? null)
                    && ($parUsuario['destino'] ?? null) == ($parCorrecto['destino'] ?? null)) {
                    $encontrado = true;
                    break;
                }
            }
            if (!$encontrado) return false;
        }
        return true;
    }

    private function validarRellenar($usuario, $correcta, $configuracion = []): bool
    {
        if (!is_array($usuario) || !is_array($correcta)) {
            return false;
        }

        if (count($usuario) !== count($correcta)) {
            return false;
        }
        
        // Si hay tipo_validacion flexible, validar contra reglas lógicas
        if (isset($configuracion['tipo_validacion']) && $configuracion['tipo_validacion'] === 'flexible') {
            return $this->validarRellenarFlexible($usuario, $configuracion);
        }
        
        // Validación estándar: comparar contra respuesta exacta
        // Si hay múltiples soluciones (array de arrays), verificar contra todas
        if (isset($correcta[0]) && is_array($correcta[0]) && isset($correcta[0][0]) && is_array($correcta[0][0])) {
            // Múltiples soluciones posibles: [[solucion1], [solucion2], ...]
            foreach ($correcta as $solucion) {
                if ($this->compararRellenar($usuario, $solucion)) {
                    return true;
                }
            }
            return false;
        }
        
        // Una sola solución
        return $this->compararRellenar($usuario, $correcta);
    }
    
    private function compararRellenar($usuario, $correcta): bool
    {
        // Verificar que todas las áreas tengan el color correcto
        foreach ($correcta as $colorCorrecto) {
            $encontrado = false;
            foreach ($usuario as $respuesta) {
                if ($respuesta['area'] === $colorCorrecto['area'] && 
                    $respuesta['color'] === $colorCorrecto['color']) {
                    $encontrado = true;
                    break;
                }
            }
            if (!$encontrado) {
                return false;
            }
        }
        return true;
    }
    
    private function validarRellenarFlexible($usuario, $configuracion): bool
    {
        // Validar que áreas adyacentes no tengan el mismo color
        // Esto requiere definir qué áreas son adyacentes en la configuración
        
        // Por ahora, validar que:
        // 1. Todas las áreas estén coloreadas
        // 2. No haya áreas duplicadas
        // 3. Los colores sean válidos
        
        $areas = $configuracion['areas'] ?? [];
        $coloresValidos = $configuracion['colores_disponibles'] ?? [];
        
        // Mapeo de colores en español a inglés
        $colorMap = [
            'verde' => 'green',
            'amarillo' => 'yellow',
            'azul' => 'blue',
            'green' => 'green',
            'yellow' => 'yellow',
            'blue' => 'blue'
        ];
        
        // Normalizar colores válidos
        $coloresValidosNormalizados = [];
        foreach ($coloresValidos as $color) {
            $normalizado = $colorMap[$color] ?? $color;
            $coloresValidosNormalizados[] = $normalizado;
            // También agregar versión en español
            foreach ($colorMap as $esp => $eng) {
                if ($eng === $normalizado) {
                    $coloresValidosNormalizados[] = $esp;
                }
            }
        }
        
        // Verificar que todas las áreas estén coloreadas
        $areasColoreadas = [];
        foreach ($usuario as $respuesta) {
            if (!isset($respuesta['area']) || !isset($respuesta['color'])) {
                return false;
            }
            
            // Normalizar color de respuesta
            $colorRespuesta = $respuesta['color'];
            $colorNormalizado = $colorMap[$colorRespuesta] ?? $colorRespuesta;
            
            // Verificar que el color sea válido
            if (!in_array($colorRespuesta, $coloresValidosNormalizados) && 
                !in_array($colorNormalizado, $coloresValidosNormalizados)) {
                return false;
            }
            
            // Verificar que no haya áreas duplicadas
            if (isset($areasColoreadas[$respuesta['area']])) {
                return false;
            }
            
            $areasColoreadas[$respuesta['area']] = $colorNormalizado;
        }
        
        // Verificar que todas las áreas requeridas estén coloreadas
        foreach ($areas as $area) {
            if (!isset($areasColoreadas[$area['id']])) {
                return false;
            }
        }
        
        // Validar regla: áreas adyacentes no pueden tener el mismo color
        // Para la pregunta 14 (flor), las reglas son:
        // - Fondo toca todos los pétalos y el centro
        // - Cada pétalo toca el fondo y el centro
        // - El centro toca el fondo y todos los pétalos
        
        // Definir adyacencias para la pregunta 14
        $adyacencias = [
            'fondo' => ['petalo1', 'petalo2', 'petalo3', 'petalo4', 'petalo5', 'centro'],
            'petalo1' => ['fondo', 'centro'],
            'petalo2' => ['fondo', 'centro'],
            'petalo3' => ['fondo', 'centro'],
            'petalo4' => ['fondo', 'centro'],
            'petalo5' => ['fondo', 'centro'],
            'centro' => ['fondo', 'petalo1', 'petalo2', 'petalo3', 'petalo4', 'petalo5']
        ];
        
        // Si hay adyacencias definidas en la configuración, usarlas
        if (isset($configuracion['adyacencias'])) {
            $adyacencias = $configuracion['adyacencias'];
        }
        
        // Verificar que áreas adyacentes no tengan el mismo color
        foreach ($areasColoreadas as $areaId => $colorArea) {
            if (isset($adyacencias[$areaId])) {
                foreach ($adyacencias[$areaId] as $areaAdyacente) {
                    if (isset($areasColoreadas[$areaAdyacente])) {
                        // Normalizar ambos colores para comparar
                        $colorAdyacente = $areasColoreadas[$areaAdyacente];
                        $colorAreaNormalizado = $colorMap[$colorArea] ?? $colorArea;
                        $colorAdyacenteNormalizado = $colorMap[$colorAdyacente] ?? $colorAdyacente;
                        
                        // Si los colores normalizados son iguales, es incorrecto
                        if ($colorAreaNormalizado === $colorAdyacenteNormalizado) {
                            return false;
                        }
                    }
                }
            }
        }
        
        return true;
    }

    private function validarTextoLibre($usuario, $correcta): bool
    {
        // Validar que el usuario envió algo
        if (!isset($usuario[0]) || empty(trim($usuario[0]))) {
            return false;
        }

        $respuestaUsuario = strtolower(trim($usuario[0]));
        
        // Normalizar $correcta para que siempre sea un array plano de strings
        if (!is_array($correcta)) {
            $correcta = [$correcta];
        }
        
        // Si es array anidado [['4']], aplanar a ['4']
        if (isset($correcta[0]) && is_array($correcta[0])) {
            $correcta = $correcta[0];
        }
        
        // Comparar con cada respuesta válida
        foreach ($correcta as $opcionCorrecta) {
            $opcionCorrecta = strtolower(trim((string)$opcionCorrecta));
            
            if ($respuestaUsuario === $opcionCorrecta) {
                return true;
            }
        }
        
        return false;
    }

    private function validarColocarPiezas($usuario, $correcta): bool
    {
        if (!is_array($usuario) || !is_array($correcta)) {
            return false;
        }

        if (count($usuario) !== count($correcta)) {
            return false;
        }
        
        // Verificar que cada colocación sea correcta
        foreach ($correcta as $colocacionCorrecta) {
            $encontrado = false;
            foreach ($usuario as $colocacionUsuario) {
                // Comparar tanto abeja como celda
                if (isset($colocacionUsuario['abeja']) && isset($colocacionUsuario['celda']) &&
                    isset($colocacionCorrecta['abeja']) && isset($colocacionCorrecta['celda'])) {
                    
                    // Convertir a string para comparación flexible
                    $abejaUsuario = (string)$colocacionUsuario['abeja'];
                    $celdaUsuario = (int)$colocacionUsuario['celda'];
                    $abejaCorrecta = (string)$colocacionCorrecta['abeja'];
                    $celdaCorrecta = (int)$colocacionCorrecta['celda'];
                    
                    if ($abejaUsuario === $abejaCorrecta && $celdaUsuario === $celdaCorrecta) {
                        $encontrado = true;
                        break;
                    }
                }
            }
            if (!$encontrado) {
                return false;
            }
        }
        
        return true;
    }

    private function validarRompecabezasHexagonos($usuario, $correcta): bool
    {
        if (!is_array($usuario) || !is_array($correcta)) {
            return false;
        }

        if (count($usuario) !== count($correcta)) {
            return false;
        }
        
        // Verificar que cada colocación sea correcta
        foreach ($correcta as $colocacionCorrecta) {
            $encontrado = false;
            foreach ($usuario as $colocacionUsuario) {
                // Comparar fila, columna y pieza
                if (isset($colocacionUsuario['fila']) && isset($colocacionUsuario['columna']) && isset($colocacionUsuario['pieza']) &&
                    isset($colocacionCorrecta['fila']) && isset($colocacionCorrecta['columna']) && isset($colocacionCorrecta['pieza'])) {
                    
                    $filaUsuario = (int)$colocacionUsuario['fila'];
                    $columnaUsuario = (int)$colocacionUsuario['columna'];
                    $piezaUsuario = (string)$colocacionUsuario['pieza'];
                    $filaCorrecta = (int)$colocacionCorrecta['fila'];
                    $columnaCorrecta = (int)$colocacionCorrecta['columna'];
                    $piezaCorrecta = (string)$colocacionCorrecta['pieza'];
                    
                    if ($filaUsuario === $filaCorrecta && $columnaUsuario === $columnaCorrecta && $piezaUsuario === $piezaCorrecta) {
                        $encontrado = true;
                        break;
                    }
                }
            }
            if (!$encontrado) {
                return false;
            }
        }
        
        return true;
    }

    private function validarColorearHexagonos($usuario, $correcta): bool
    {
        if (!is_array($usuario) || !is_array($correcta)) {
            return false;
        }

        if (count($usuario) !== count($correcta)) {
            return false;
        }
        
        // Verificar que cada colocación sea correcta
        foreach ($correcta as $colocacionCorrecta) {
            $encontrado = false;
            foreach ($usuario as $colocacionUsuario) {
                // Comparar posición y color
                if (isset($colocacionUsuario['posicion']) && isset($colocacionUsuario['color']) &&
                    isset($colocacionCorrecta['posicion']) && isset($colocacionCorrecta['color'])) {
                    
                    $posUsuario = $colocacionUsuario['posicion'];
                    $posCorrecta = $colocacionCorrecta['posicion'];
                    
                    if (is_array($posUsuario) && is_array($posCorrecta) &&
                        count($posUsuario) === 2 && count($posCorrecta) === 2) {
                        
                        $filaUsuario = (int)$posUsuario[0];
                        $columnaUsuario = (int)$posUsuario[1];
                        $colorUsuario = (string)$colocacionUsuario['color'];
                        $filaCorrecta = (int)$posCorrecta[0];
                        $columnaCorrecta = (int)$posCorrecta[1];
                        $colorCorrecta = (string)$colocacionCorrecta['color'];
                        
                        if ($filaUsuario === $filaCorrecta && $columnaUsuario === $columnaCorrecta && $colorUsuario === $colorCorrecta) {
                            $encontrado = true;
                            break;
                        }
                    }
                }
            }
            if (!$encontrado) {
                return false;
            }
        }
        
        return true;
    }

    private function validarTejerAlfombra($usuario, $correcta): bool
    {
        if (!is_array($usuario) || !is_array($correcta)) {
            return false;
        }

        // Verificar que tengan el mismo número de filas
        if (count($usuario) !== count($correcta)) {
            return false;
        }

        // Comparar cada fila
        for ($i = 0; $i < count($usuario); $i++) {
            if (!is_array($usuario[$i]) || !is_array($correcta[$i])) {
                return false;
            }

            // Verificar que tengan el mismo número de columnas
            if (count($usuario[$i]) !== count($correcta[$i])) {
                return false;
            }

            // Comparar cada celda
            for ($j = 0; $j < count($usuario[$i]); $j++) {
                $usuarioValor = (string)($usuario[$i][$j] ?? '');
                $correctaValor = (string)($correcta[$i][$j] ?? '');
                
                if ($usuarioValor !== $correctaValor) {
                    return false;
                }
            }
        }

        return true;
    }

    private function validarCompletar($usuario, $correcta, $configuracion = []): bool
    {
        if (!$usuario || !$correcta) {
            return false;
        }

        // Respuesta como string (ej: 'XXXBNBXNXNB')
        if (is_string($correcta)) {
            $correcta = [$correcta];
        }
        if (isset($correcta[0]) && is_string($correcta[0]) && !is_array($correcta[0])) {
            $respuestaUsuario = is_array($usuario) ? ($usuario[0] ?? '') : (string)$usuario;
            foreach ((array)$correcta as $opcion) {
                if (trim(strtolower((string)$respuestaUsuario)) === trim(strtolower((string)$opcion))) {
                    return true;
                }
            }
            return false;
        }

        // Respuesta como array de ids (orden importa salvo que config indique comparar como conjunto)
        if (isset($correcta[0]) && is_string($correcta[0])) {
            $usuarioFlat = is_array($usuario) ? $usuario : [$usuario];
            if (count($usuarioFlat) !== count($correcta)) {
                return false;
            }
            if (!empty($configuracion['comparar_como_conjunto'])) {
                sort($usuarioFlat);
                $correctaSorted = $correcta;
                sort($correctaSorted);
                return $usuarioFlat === $correctaSorted;
            }
            return $usuarioFlat === $correcta;
        }

        // Respuesta como objeto/array asociativo (ej: {martes: 'manzana', miercoles: 'pera})
        if (is_array($correcta) && isset($correcta[0]) && is_array($correcta[0])) {
            foreach ($correcta as $solucion) {
                if ($this->compararCompletarObjeto($usuario, $solucion)) {
                    return true;
                }
            }
            return false;
        }

        if (is_array($correcta) && !isset($correcta[0])) {
            return $this->compararCompletarObjeto($usuario, $correcta);
        }

        return false;
    }

    private function compararCompletarObjeto($usuario, $correcta): bool
    {
        if (!is_array($usuario) || !is_array($correcta)) {
            return false;
        }
        foreach ($correcta as $key => $valorCorrecto) {
            $valorUsuario = $usuario[$key] ?? null;
            if (is_array($valorCorrecto)) {
                if (!is_array($valorUsuario) || count($valorUsuario) !== count($valorCorrecto)) {
                    return false;
                }
                sort($valorUsuario);
                sort($valorCorrecto);
                if ($valorUsuario !== $valorCorrecto) {
                    return false;
                }
            } elseif (($usuario[$key] ?? null) != $valorCorrecto) {
                return false;
            }
        }
        return true;
    }

    private function formatearRespuestaCorrecta($tipo, $correcta): string
    {
        switch ($tipo) {
            case 'seleccion_simple':
                $opciones = is_array($correcta[0] ?? null) ? $correcta[0] : $correcta;
                return 'Opción correcta: ' . implode(', ', $opciones);
            
            case 'seleccion_multiple':
                $opciones = is_array($correcta[0] ?? null) && is_string($correcta[0]) 
                    ? $correcta 
                    : ($correcta[0] ?? []);
                return 'Opciones correctas: ' . implode(', ', $opciones);
            
            case 'ordenar':
                if (isset($correcta[0]) && is_array($correcta[0])) {
                    return 'Una respuesta posible: ' . implode(' → ', $correcta[0]);
                }
                return 'Orden correcto: ' . implode(' → ', $correcta);
            
            case 'grid_seleccion':
                $texto = 'Celdas correctas: ';
                foreach ($correcta as $celda) {
                    $texto .= "Fila " . ($celda['fila'] + 1) . ", Columna " . ($celda['columna'] + 1) . "; ";
                }
                return rtrim($texto, '; ');
            
            case 'emparejar':
                $texto = 'Emparejamientos correctos: ';
                foreach ($correcta as $par) {
                    $texto .= "{$par['objeto']} → {$par['destino']}, ";
                }
                return rtrim($texto, ', ');
            
            case 'texto_libre':
                $opciones = is_array($correcta[0] ?? null) ? $correcta : [$correcta[0] ?? 'N/A'];
                return 'Respuesta correcta: ' . implode(' o ', $opciones);
            
            case 'rellenar':
                return 'Revisa la explicación para ver la solución completa.';
            
            case 'colocar_piezas':
                $texto = 'Colocación correcta: ';
                foreach ($correcta as $colocacion) {
                    $texto .= "Abeja {$colocacion['abeja']} en celda {$colocacion['celda']}, ";
                }
                return rtrim($texto, ', ');
            
            case 'rompecabezas_hexagonos':
                $texto = 'Colocación correcta: ';
                foreach ($correcta as $colocacion) {
                    $texto .= "Pieza {$colocacion['pieza']} en fila {$colocacion['fila']}, columna {$colocacion['columna']}, ";
                }
                return rtrim($texto, ', ');
            
            case 'colorear_hexagonos':
                $texto = 'Colocación correcta: ';
                foreach ($correcta as $colocacion) {
                    $pos = $colocacion['posicion'];
                    $texto .= "Fila {$pos[0]}, columna {$pos[1]}: {$colocacion['color']}, ";
                }
                return rtrim($texto, ', ');
            
            case 'tejer_alfombra':
                return 'Revisa la explicación para ver la solución completa del grid.';

            case 'completar':
                if (is_array($correcta) && isset($correcta[0]) && is_string($correcta[0]) && array_is_list($correcta)) {
                    return 'Opciones correctas: ' . implode(', ', $correcta);
                }
                return 'Revisa la explicación para ver la solución completa.';

            default:
                return '';
        }
    }
}