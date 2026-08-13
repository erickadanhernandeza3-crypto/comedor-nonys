<?php
/**
 * Controlador: menú público.
 * Arma los datos de la portada y la respuesta AJAX de la comida del día.
 */
require_once __DIR__ . '/../includes/funciones.php';

function pagina_inicio(): array
{
    $hoy    = date('Y-m-d');
    $pedido = pedido_actual();

    return [
        'titulo'      => config('nombre_negocio') . ' — Menú y comida del día',
        'hoy'         => $hoy,
        'items'       => menu_del_dia($hoy),
        'categorias'  => categorias_con_platillos(),
        'pedido'      => $pedido,
        'totalPedido' => total_pedido($pedido),
    ];
}

/**
 * Comida del día para el fetch del front. Se manda el HTML ya armado con la
 * misma vista parcial de la portada, para que no haya dos versiones del marcado.
 */
function menu_dia_publico(array $entrada): array
{
    $fecha = fecha($entrada);
    $items = menu_del_dia($fecha);

    return [
        'fecha'       => $fecha,
        'fecha_larga' => fecha_larga($fecha),
        'actualizado' => date('H:i'),
        'total'       => count($items),
        'items'       => array_map(static fn(array $item): array => [
            'id'          => (int) $item['id'],
            'nombre'      => $item['nombre'],
            'descripcion' => $item['descripcion'],
            'precio'      => (float) $item['precio'],
            'disponible'  => (bool) $item['disponible'],
        ], $items),
        'html' => vista_html('publico/parciales/menu_dia', ['items' => $items]),
    ];
}
