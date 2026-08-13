<?php
/**
 * Lectura de lo que llega en la petición.
 * Los controladores nunca tocan $_POST directo: reciben un arreglo y lo leen
 * con estas funciones, que ya limpian y convierten al tipo correcto.
 */

function texto(array $datos, string $clave, string $porDefecto = ''): string
{
    $valor = trim((string) ($datos[$clave] ?? ''));

    return $valor !== '' ? $valor : $porDefecto;
}

function entero(array $datos, string $clave, int $porDefecto = 0): int
{
    return isset($datos[$clave]) && $datos[$clave] !== '' ? (int) $datos[$clave] : $porDefecto;
}

function decimal(array $datos, string $clave, float $porDefecto = 0.0): float
{
    return isset($datos[$clave]) && $datos[$clave] !== '' ? (float) $datos[$clave] : $porDefecto;
}

/** Una casilla marcada llega como "1"/"on"; si no viene, es 0. */
function casilla(array $datos, string $clave): int
{
    $valor = $datos[$clave] ?? null;

    return ($valor === null || $valor === '' || $valor === '0' || $valor === 'false') ? 0 : 1;
}

/** Un archivo subido, tal como lo dejó PHP en $_FILES. */
function archivo(array $datos, string $clave): ?array
{
    $archivo = $datos['archivos'][$clave] ?? null;

    return is_array($archivo) ? $archivo : null;
}

function es_fecha(string $fecha): bool
{
    return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) && strtotime($fecha) !== false;
}

/** Fecha del formulario; si viene vacía o mal escrita, se usa hoy. */
function fecha(array $datos, string $clave = 'fecha'): string
{
    $valor = texto($datos, $clave);

    return es_fecha($valor) ? $valor : date('Y-m-d');
}
