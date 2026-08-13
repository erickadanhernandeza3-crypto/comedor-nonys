<?php
/**
 * Controlador: el pedido del cliente en el menú público.
 * Atiende las acciones del carrito y, al final, arma el mensaje de WhatsApp.
 */
require_once __DIR__ . '/../includes/funciones.php';

function controlador_pedido(string $accion, array $entrada): array
{
    switch ($accion) {
        case 'ver':      return estado_pedido();
        case 'agregar':  return pedido_agregar($entrada);
        case 'cantidad': return pedido_cantidad($entrada);
        case 'quitar':   return pedido_quitar($entrada);
        case 'vaciar':   vaciar_pedido(); return estado_pedido('Se vació tu pedido.', 'warning');
        case 'enviar':   return pedido_enviar($entrada);
    }

    return respuesta_error('Acción no reconocida en el pedido.');
}

/** Respuesta estándar: aviso + el panel del pedido repintado. */
function estado_pedido(string $mensaje = '', string $tipo = 'success'): array
{
    $items = pedido_actual();
    $total = total_pedido($items);

    return respuesta_ok($mensaje, [
        'tipo'       => $tipo,
        'fragmentos' => [
            '#panel-pedido' => vista_html('publico/parciales/pedido', [
                'items' => $items,
                'total' => $total,
            ]),
        ],
        'datos' => [
            'piezas'      => piezas_pedido($items),
            'total'       => $total,
            'texto_total' => precio($total),
        ],
    ]);
}

/** Lee tipo e id del formulario y comprueba que el platillo exista. */
function platillo_pedido(array $entrada): ?array
{
    $tipo = texto($entrada, 'tipo');

    if (!in_array($tipo, TIPOS_DE_PEDIDO, true)) {
        return null;
    }

    $fila = platillo_del_pedido($tipo, entero($entrada, 'id'));

    return $fila ? ['tipo' => $tipo, 'fila' => $fila] : null;
}

function pedido_agregar(array $entrada): array
{
    $elegido = platillo_pedido($entrada);

    if (!$elegido) {
        return respuesta_error('Ese platillo ya no está en el menú.');
    }

    if (!$elegido['fila']['disponible']) {
        return respuesta_error('«' . $elegido['fila']['nombre'] . '» se acabó por hoy.', 'warning');
    }

    agregar_al_pedido($elegido['tipo'], (int) $elegido['fila']['id'], max(1, entero($entrada, 'cantidad', 1)));

    return estado_pedido('Se agregó «' . $elegido['fila']['nombre'] . '» a tu pedido.');
}

function pedido_cantidad(array $entrada): array
{
    $elegido = platillo_pedido($entrada);

    if (!$elegido) {
        return respuesta_error('Ese platillo ya no está en el menú.');
    }

    cambiar_cantidad_pedido($elegido['tipo'], (int) $elegido['fila']['id'], entero($entrada, 'cantidad'));

    return estado_pedido();
}

function pedido_quitar(array $entrada): array
{
    $elegido = platillo_pedido($entrada);

    if (!$elegido) {
        return estado_pedido();
    }

    quitar_del_pedido($elegido['tipo'], (int) $elegido['fila']['id']);

    return estado_pedido('Se quitó «' . $elegido['fila']['nombre'] . '» de tu pedido.', 'warning');
}

/**
 * Revisa que todo siga disponible y devuelve el enlace de WhatsApp con el
 * pedido ya redactado. El navegador solo tiene que abrirlo.
 */
function pedido_enviar(array $entrada): array
{
    $items = pedido_actual();

    if (!$items) {
        return respuesta_error('Todavía no has agregado nada a tu pedido.', 'warning');
    }

    $agotados = array_filter($items, static fn(array $i): bool => !$i['disponible']);

    if ($agotados) {
        $nombres = implode(', ', array_column($agotados, 'nombre'));

        return respuesta_error(
            'Se acabó: ' . $nombres . '. Quítalo de tu pedido para poder mandarlo.',
            'warning'
        );
    }

    return respuesta_ok('Te abrimos WhatsApp con tu pedido.', [
        'datos' => ['whatsapp' => enlace_whatsapp(mensaje_del_pedido($items, texto($entrada, 'nota')))],
    ]);
}

/** El texto que le llega al comedor por WhatsApp. */
function mensaje_del_pedido(array $items, string $nota = ''): string
{
    $lineas = ['¡Hola ' . config('nombre_negocio') . '! Quiero hacer este pedido:', ''];

    foreach ($items as $item) {
        $lineas[] = sprintf(
            '• %d x %s%s — %s',
            $item['cantidad'],
            $item['nombre'],
            $item['del_dia'] ? ' (comida del día)' : '',
            precio($item['importe'])
        );
    }

    $lineas[] = '';
    $lineas[] = 'Total: ' . precio(total_pedido($items));

    if ($nota !== '') {
        $lineas[] = '';
        $lineas[] = 'Nota: ' . $nota;
    }

    return implode("\n", $lineas);
}
