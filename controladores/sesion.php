<?php
/**
 * Controlador: entrada al panel.
 * Es el único recurso del router que no exige sesión previa.
 */
require_once __DIR__ . '/../includes/sesion.php';

function controlador_sesion(string $accion, array $entrada): array
{
    if ($accion === 'entrar') {
        return sesion_entrar($entrada);
    }

    return respuesta_error('Acción no reconocida en la sesión.');
}

function sesion_entrar(array $entrada): array
{
    $nombre = texto($entrada, 'usuario');
    $clave  = (string) ($entrada['contrasena'] ?? '');

    if ($nombre === '' || $clave === '') {
        return respuesta_error('Escribe tu usuario y tu contraseña.');
    }

    $usuario = usuario_por_nombre($nombre);

    if (!$usuario || !password_verify($clave, $usuario['contrasena'])) {
        return respuesta_error('Usuario o contraseña incorrectos.');
    }

    iniciar_sesion($usuario);

    return respuesta_ok('¡Bienvenido, ' . ($usuario['nombre'] ?: $usuario['usuario']) . '!', [
        'datos' => ['redirigir' => 'menu_dia.php'],
    ]);
}
