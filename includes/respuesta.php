<?php
/**
 * Respuestas de los controladores.
 *
 * Un controlador siempre devuelve el mismo arreglo:
 *   ok         → si la acción se completó
 *   tipo       → color del aviso en pantalla (success | danger | warning)
 *   mensaje    → lo que lee la persona
 *   fragmentos → [ selector CSS => HTML ] que el JS vuelve a pintar
 *   datos      → valores sueltos que el JS pueda necesitar
 */

function respuesta_ok(string $mensaje = '', array $extra = []): array
{
    return array_merge([
        'ok'         => true,
        'tipo'       => 'success',
        'mensaje'    => $mensaje,
        'fragmentos' => [],
        'datos'      => [],
    ], $extra);
}

function respuesta_error(string $mensaje, string $tipo = 'danger', array $extra = []): array
{
    return array_merge([
        'ok'         => false,
        'tipo'       => $tipo,
        'mensaje'    => $mensaje,
        'fragmentos' => [],
        'datos'      => [],
    ], $extra);
}

/** Cierra la petición mandando la respuesta como JSON. */
function responder_json(array $respuesta, int $codigo = 200): void
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');

    // JSON_INVALID_UTF8_SUBSTITUTE evita que un solo byte raro en la base deje
    // la respuesta vacía: se sustituye el carácter y el panel sigue funcionando.
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
