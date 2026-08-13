<?php
/**
 * Controlador: comida del día.
 * Recibe la acción y los datos del formulario, valida, manda al modelo y
 * devuelve el aviso más la lista ya repintada.
 */
require_once __DIR__ . '/../includes/sesion.php';

function controlador_menu_dia(string $accion, array $entrada): array
{
    $fecha = fecha($entrada);

    switch ($accion) {
        case 'listar':         return estado_menu_dia($fecha);
        case 'agregar':        return menu_dia_agregar($fecha, $entrada);
        case 'editar':         return menu_dia_editar($fecha, $entrada);
        case 'eliminar':       return menu_dia_eliminar($fecha, $entrada);
        case 'copiar':         return menu_dia_copiar($fecha, $entrada);
        case 'disponibilidad': return menu_dia_disponibilidad($entrada);
    }

    return respuesta_error('Acción no reconocida en la comida del día.');
}

/** Respuesta estándar: aviso + lista del día repintada. */
function estado_menu_dia(string $fecha, string $mensaje = '', string $tipo = 'success'): array
{
    $items = menu_del_dia($fecha);

    return respuesta_ok($mensaje, [
        'tipo'       => $tipo,
        'fragmentos' => [
            '#lista-menu-dia' => vista_html('admin/parciales/lista_menu_dia', [
                'items' => $items,
                'fecha' => $fecha,
            ]),
        ],
        'datos' => [
            'fecha'       => $fecha,
            'fecha_larga' => fecha_larga($fecha),
            'total'       => count($items),
        ],
    ]);
}

function menu_dia_agregar(string $fecha, array $entrada): array
{
    $platilloId  = entero($entrada, 'platillo_id');
    $nombre      = texto($entrada, 'nombre');
    $descripcion = texto($entrada, 'descripcion');
    $precio      = decimal($entrada, 'precio');

    // Si eligieron un platillo del catálogo, se completa lo que no escribieron.
    if ($platilloId) {
        $base = platillo($platilloId);
        if ($base) {
            $nombre      = $nombre ?: $base['nombre'];
            $descripcion = $descripcion ?: (string) $base['descripcion'];
            $precio      = $precio ?: (float) $base['precio'];
        }
    }

    if ($nombre === '') {
        return respuesta_error('Escribe el nombre del platillo.');
    }

    agregar_item_dia($fecha, [
        'platillo_id' => $platilloId,
        'nombre'      => $nombre,
        'descripcion' => $descripcion,
        'precio'      => $precio,
        'disponible'  => casilla($entrada, 'disponible'),
    ]);

    return estado_menu_dia($fecha, 'Se agregó «' . $nombre . '» a la comida del día.');
}

function menu_dia_editar(string $fecha, array $entrada): array
{
    $id     = entero($entrada, 'id');
    $nombre = texto($entrada, 'nombre');

    if (!$id || !item_del_dia($id)) {
        return respuesta_error('Ese platillo ya no está en el menú del día.');
    }

    if ($nombre === '') {
        return respuesta_error('Escribe el nombre del platillo.');
    }

    actualizar_item_dia($id, [
        'nombre'      => $nombre,
        'descripcion' => texto($entrada, 'descripcion'),
        'precio'      => decimal($entrada, 'precio'),
        'disponible'  => casilla($entrada, 'disponible'),
    ]);

    return estado_menu_dia($fecha, 'Se guardó «' . $nombre . '».');
}

function menu_dia_eliminar(string $fecha, array $entrada): array
{
    $item = item_del_dia(entero($entrada, 'id'));

    if (!$item) {
        return respuesta_error('Ese platillo ya no está en el menú del día.');
    }

    eliminar_item_dia((int) $item['id']);

    return estado_menu_dia($fecha, 'Se quitó «' . $item['nombre'] . '» del menú del día.');
}

function menu_dia_copiar(string $fecha, array $entrada): array
{
    $origen = texto($entrada, 'fecha_origen');

    if (!es_fecha($origen)) {
        return respuesta_error('Elige la fecha de la que quieres copiar.');
    }

    if ($origen === $fecha) {
        return respuesta_error('Esa es la misma fecha que estás viendo.', 'warning');
    }

    $copiados = copiar_menu_dia($fecha, $origen);

    if (!$copiados) {
        return estado_menu_dia($fecha, 'Ese día no tenía menú capturado.', 'warning');
    }

    return estado_menu_dia(
        $fecha,
        'Se copiaron ' . $copiados . ' platillos del ' . fecha_larga($origen) . '.'
    );
}

/** El interruptor "Disponible / Se acabó": solo cambia el renglón tocado. */
function menu_dia_disponibilidad(array $entrada): array
{
    $item = item_del_dia(entero($entrada, 'id'));

    if (!$item) {
        return respuesta_error('Ese platillo ya no está en el menú del día.');
    }

    $disponible = casilla($entrada, 'disponible') === 1;
    cambiar_disponible_item_dia((int) $item['id'], $disponible);

    return respuesta_ok('', [
        'datos' => [
            'id'         => (int) $item['id'],
            'disponible' => $disponible,
            'etiqueta'   => $disponible ? 'Disponible' : 'Se acabó',
        ],
    ]);
}
