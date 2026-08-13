<?php
/**
 * Controlador: datos del negocio y contraseña del panel.
 */
require_once __DIR__ . '/../includes/sesion.php';

function controlador_ajustes(string $accion, array $entrada): array
{
    switch ($accion) {
        case 'datos':      return ajustes_datos($entrada);
        case 'contrasena': return ajustes_contrasena($entrada);
    }

    return respuesta_error('Acción no reconocida en los ajustes.');
}

function ajustes_datos(array $entrada): array
{
    // Solo se tocan las claves que vinieron en el formulario: si una petición
    // llega incompleta, las demás se quedan como están en lugar de vaciarse.
    $valores = [];
    foreach (array_keys(campos_del_negocio()) as $clave) {
        if (array_key_exists($clave, $entrada)) {
            $valores[$clave] = texto($entrada, $clave);
        }
    }

    if (!$valores) {
        return respuesta_error('No llegó ningún dato que guardar.');
    }

    if (isset($valores['nombre_negocio']) && $valores['nombre_negocio'] === '') {
        return respuesta_error('El nombre del negocio no puede quedar vacío.');
    }

    guardar_configuracion($valores);

    // El nombre viaja de vuelta porque sale en el título y en la barra del panel.
    return respuesta_ok('Datos del negocio actualizados.', [
        'datos' => ['nombre_negocio' => config('nombre_negocio')],
    ]);
}

function ajustes_contrasena(array $entrada): array
{
    $usuario = usuario_actual();
    $actual  = (string) ($entrada['actual'] ?? '');
    $nueva   = (string) ($entrada['nueva'] ?? '');
    $repetir = (string) ($entrada['repetir'] ?? '');

    if (!contrasena_correcta((int) $usuario['id'], $actual)) {
        return respuesta_error('La contraseña actual no es correcta.');
    }

    if (strlen($nueva) < 8) {
        return respuesta_error('La nueva contraseña debe tener al menos 8 caracteres.');
    }

    if ($nueva !== $repetir) {
        return respuesta_error('La confirmación no coincide con la nueva contraseña.');
    }

    guardar_contrasena((int) $usuario['id'], $nueva);

    return respuesta_ok('Contraseña actualizada.', ['limpiar' => true]);
}
