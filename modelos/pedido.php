<?php
/**
 * Modelo: el pedido que va armando la persona mientras ve el menú.
 *
 * En la sesión solo se guarda qué se pidió y cuánto ("platillo:7" => 2). El
 * nombre y el precio se leen de la base cada vez que se muestra, así nadie
 * puede cambiarlos desde el navegador y el total siempre es el de la casa.
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/platillos.php';
require_once __DIR__ . '/menu_dia.php';

/** De dónde puede venir un renglón: del menú general o de la comida del día. */
const TIPOS_DE_PEDIDO = ['platillo', 'dia'];

/** Tope por renglón, para que un dedo resbalado no pida 500 cafés. */
const MAXIMO_POR_PLATILLO = 30;

function clave_pedido(string $tipo, int $id): string
{
    return $tipo . ':' . $id;
}

/** Lo crudo que hay en la sesión: [clave => cantidad]. */
function pedido_guardado(): array
{
    abrir_sesion();

    return $_SESSION['pedido'] ?? [];
}

function guardar_pedido(array $renglones): void
{
    abrir_sesion();

    $_SESSION['pedido'] = $renglones;
}

function vaciar_pedido(): void
{
    guardar_pedido([]);
}

/** Busca el platillo en la tabla que le toca. */
function platillo_del_pedido(string $tipo, int $id): ?array
{
    return $tipo === 'dia' ? item_del_dia($id) : platillo($id);
}

/**
 * El pedido completo, ya con nombres y precios de la base.
 * Los renglones que ya no existen en el menú se descartan solos.
 */
function pedido_actual(): array
{
    $renglones = pedido_guardado();
    $items     = [];
    $limpio    = [];

    foreach ($renglones as $clave => $cantidad) {
        [$tipo, $id] = array_pad(explode(':', (string) $clave, 2), 2, '');
        $fila = in_array($tipo, TIPOS_DE_PEDIDO, true) ? platillo_del_pedido($tipo, (int) $id) : null;

        if (!$fila) {
            continue;
        }

        $cantidad          = max(1, min(MAXIMO_POR_PLATILLO, (int) $cantidad));
        $limpio[$clave]    = $cantidad;
        $items[]           = [
            'tipo'       => $tipo,
            'id'         => (int) $fila['id'],
            'clave'      => $clave,
            'nombre'     => $fila['nombre'],
            'precio'     => (float) $fila['precio'],
            'cantidad'   => $cantidad,
            'importe'    => (float) $fila['precio'] * $cantidad,
            'disponible' => (bool) $fila['disponible'],
            'del_dia'    => $tipo === 'dia',
        ];
    }

    if ($limpio !== $renglones) {
        guardar_pedido($limpio);
    }

    return $items;
}

function total_pedido(array $items): float
{
    return array_sum(array_column($items, 'importe'));
}

/** Cuántas piezas lleva en total (para el contador de la barra). */
function piezas_pedido(array $items): int
{
    return (int) array_sum(array_column($items, 'cantidad'));
}

function agregar_al_pedido(string $tipo, int $id, int $cantidad = 1): void
{
    $renglones = pedido_guardado();
    $clave     = clave_pedido($tipo, $id);
    $suma      = ($renglones[$clave] ?? 0) + $cantidad;

    $renglones[$clave] = max(1, min(MAXIMO_POR_PLATILLO, $suma));

    guardar_pedido($renglones);
}

/** Cambia la cantidad de un renglón; en cero o menos, lo quita. */
function cambiar_cantidad_pedido(string $tipo, int $id, int $cantidad): void
{
    $renglones = pedido_guardado();
    $clave     = clave_pedido($tipo, $id);

    if ($cantidad < 1) {
        unset($renglones[$clave]);
    } else {
        $renglones[$clave] = min(MAXIMO_POR_PLATILLO, $cantidad);
    }

    guardar_pedido($renglones);
}

function quitar_del_pedido(string $tipo, int $id): void
{
    $renglones = pedido_guardado();
    unset($renglones[clave_pedido($tipo, $id)]);

    guardar_pedido($renglones);
}
