<?php
/**
 * Router AJAX del panel: todo el JS del administrador pega aquí.
 *
 * POST { recurso, accion, csrf, ...campos del formulario }
 * Respuesta JSON: { ok, tipo, mensaje, fragmentos, datos }
 */
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../controladores/sesion.php';
require_once __DIR__ . '/../controladores/menu_dia.php';
require_once __DIR__ . '/../controladores/platillos.php';
require_once __DIR__ . '/../controladores/ajustes.php';

/** Recurso => función del controlador que lo atiende. */
const CONTROLADORES = [
    'sesion'    => 'controlador_sesion',
    'menu_dia'  => 'controlador_menu_dia',
    'platillos' => 'controlador_platillos',
    'ajustes'   => 'controlador_ajustes',
];

/** Lo único que se puede pedir sin haber entrado todavía. */
const RECURSOS_LIBRES = ['sesion'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(respuesta_error('Esta dirección solo atiende peticiones POST.'), 405);
}

// Si el envío pasó de post_max_size, PHP descarta $_POST completo y llegaría
// aquí como "recurso no reconocido". Se avisa de lo que en verdad pasó.
if (!$_POST && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    responder_json(respuesta_error(
        'El envío pesa demasiado (límite del servidor: ' . ini_get('post_max_size') . '). Sube una foto más ligera.'
    ), 413);
}

$recurso = texto($_POST, 'recurso');
$accion  = texto($_POST, 'accion');

if (!isset(CONTROLADORES[$recurso])) {
    responder_json(respuesta_error('Recurso no reconocido: ' . $recurso), 404);
}

// Primero la sesión: así, si ya caducó, se manda al login en vez de hablar del token.
if (!in_array($recurso, RECURSOS_LIBRES, true) && !usuario_actual()) {
    responder_json(respuesta_error('Tu sesión terminó. Vuelve a entrar.', 'danger', [
        'datos' => ['redirigir' => 'login.php'],
    ]), 401);
}

if (!csrf_valido($_POST)) {
    responder_json(respuesta_error('Vuelve a cargar la página e inténtalo de nuevo.'), 419);
}

$controlador = CONTROLADORES[$recurso];

// Los archivos viajan junto al resto del formulario, bajo la llave "archivos".
$entrada = $_POST;
$entrada['archivos'] = $_FILES;

responder_json($controlador($accion, $entrada));
