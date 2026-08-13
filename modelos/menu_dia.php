<?php
/**
 * Modelo: comida del día (un renglón por platillo ofrecido en una fecha).
 */
require_once __DIR__ . '/../config/db.php';

function menu_del_dia(?string $fecha = null): array
{
    return consultar(
        'SELECT * FROM menu_del_dia WHERE fecha = ? ORDER BY orden, id',
        [$fecha ?: date('Y-m-d')]
    );
}

function item_del_dia(int $id): ?array
{
    return consultar_una('SELECT * FROM menu_del_dia WHERE id = ?', [$id], 'i');
}

/** Siguiente número de orden libre para esa fecha. */
function siguiente_orden_dia(string $fecha): int
{
    $fila = consultar_una(
        'SELECT COALESCE(MAX(orden), 0) + 1 AS siguiente FROM menu_del_dia WHERE fecha = ?',
        [$fecha]
    );

    return (int) ($fila['siguiente'] ?? 1);
}

function agregar_item_dia(string $fecha, array $datos): int
{
    return insertar(
        'INSERT INTO menu_del_dia (fecha, platillo_id, nombre, descripcion, precio, disponible, orden)
         VALUES (?, ?, ?, ?, ?, ?, ?)',
        [
            $fecha,
            $datos['platillo_id'] ?: null,
            $datos['nombre'],
            $datos['descripcion'],
            $datos['precio'],
            $datos['disponible'],
            siguiente_orden_dia($fecha),
        ],
        'sissdii'
    );
}

function actualizar_item_dia(int $id, array $datos): int
{
    return ejecutar(
        'UPDATE menu_del_dia SET nombre = ?, descripcion = ?, precio = ?, disponible = ? WHERE id = ?',
        [$datos['nombre'], $datos['descripcion'], $datos['precio'], $datos['disponible'], $id],
        'ssdii'
    );
}

function eliminar_item_dia(int $id): int
{
    return ejecutar('DELETE FROM menu_del_dia WHERE id = ?', [$id], 'i');
}

/** Copia los platillos de una fecha a otra. Devuelve cuántos se copiaron. */
function copiar_menu_dia(string $destino, string $origen): int
{
    return ejecutar(
        'INSERT INTO menu_del_dia (fecha, platillo_id, nombre, descripcion, precio, disponible, orden)
         SELECT ?, platillo_id, nombre, descripcion, precio, 1, orden
           FROM menu_del_dia WHERE fecha = ?',
        [$destino, $origen]
    );
}

function cambiar_disponible_item_dia(int $id, bool $disponible): int
{
    return ejecutar(
        'UPDATE menu_del_dia SET disponible = ? WHERE id = ?',
        [$disponible ? 1 : 0, $id],
        'ii'
    );
}
