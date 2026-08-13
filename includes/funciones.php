<?php
/**
 * Arranque de la aplicación y funciones de presentación.
 * Todo punto de entrada (index.php, admin/*.php, api/*.php) incluye este archivo:
 * desde aquí se cargan la conexión y los modelos.
 */

define('RUTA_APP', dirname(__DIR__));

require_once RUTA_APP . '/config/db.php';
require_once RUTA_APP . '/modelos/configuracion.php';
require_once RUTA_APP . '/modelos/categorias.php';
require_once RUTA_APP . '/modelos/platillos.php';
require_once RUTA_APP . '/modelos/menu_dia.php';
require_once RUTA_APP . '/modelos/usuarios.php';
require_once RUTA_APP . '/modelos/pedido.php';
require_once RUTA_APP . '/includes/peticion.php';
require_once RUTA_APP . '/includes/respuesta.php';
require_once RUTA_APP . '/includes/imagenes.php';

/**
 * Última red de seguridad: si algo truena (casi siempre la base de datos), el
 * cliente ve un aviso decente en vez de una página en blanco, y el detalle
 * queda en el registro del servidor para que lo veamos nosotros.
 */
set_exception_handler(static function (Throwable $problema): void {
    error_log("Comedor Nony's: " . $problema->getMessage()
        . ' en ' . $problema->getFile() . ':' . $problema->getLine());

    http_response_code(503);

    if (peticion_de_datos()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok'         => false,
            'tipo'       => 'danger',
            'mensaje'    => 'El servidor no está respondiendo. Inténtalo en un momento.',
            'fragmentos' => [],
            'datos'      => [],
        ], JSON_UNESCAPED_UNICODE);

        return;
    }

    vista('publico/aviso_error');
});

/** ¿La petición esperaba datos (AJAX) en lugar de una página? */
function peticion_de_datos(): bool
{
    $script = (string) ($_SERVER['SCRIPT_NAME'] ?? '');

    return substr($script, -7) === 'api.php' || strpos($script, '/api/') !== false;
}

/** Abre la sesión del navegador si todavía no está abierta. */
function abrir_sesion(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/** Imprime una vista de la carpeta vistas/ con los datos que reciba. */
function vista(string $_ruta, array $_datos = []): void
{
    extract($_datos, EXTR_SKIP);

    require RUTA_APP . '/vistas/' . $_ruta . '.php';
}

/** La misma vista, pero devuelta como texto (así viaja el HTML por AJAX). */
function vista_html(string $ruta, array $datos = []): string
{
    ob_start();
    vista($ruta, $datos);

    return (string) ob_get_clean();
}

/** Escapa texto para imprimirlo en HTML. */
function e(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function precio(float $monto): string
{
    return '$' . number_format($monto, 2);
}

/** Precio listo para un <input type="number">: 95.00, sin separador de miles. */
function precio_campo(float $monto): string
{
    return number_format($monto, 2, '.', '');
}

/** Fecha larga en español, sin depender de la extensión intl. */
function fecha_larga(string $fecha): string
{
    $dias  = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio',
              'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

    $t = strtotime($fecha);

    return sprintf(
        '%s %d de %s de %d',
        $dias[(int) date('w', $t)],
        (int) date('j', $t),
        $meses[(int) date('n', $t) - 1],
        (int) date('Y', $t)
    );
}

/**
 * Dirección de la foto de un platillo, o cadena vacía si no tiene.
 * Primero la que está guardada en la base, luego la liga o ruta escrita a mano.
 *
 * @param string $base Prefijo para salir de una subcarpeta ('../' en el panel).
 */
function url_foto(array $platillo, string $base = ''): string
{
    if (!empty($platillo['foto_tipo'])) {
        return $base . 'api/foto.php?id=' . (int) $platillo['id']
             . '&v=' . (int) ($platillo['foto_version'] ?? 0);
    }

    $imagen = trim((string) ($platillo['imagen'] ?? ''));

    if ($imagen === '') {
        return '';
    }

    return strpos($imagen, 'http') === 0 ? $imagen : $base . $imagen;
}

/** Arma el enlace de WhatsApp con un mensaje ya redactado. */
function enlace_whatsapp(string $mensaje): string
{
    $numero = preg_replace('/\D/', '', (string) config('whatsapp'));

    return 'https://wa.me/' . $numero . '?text=' . rawurlencode($mensaje);
}
