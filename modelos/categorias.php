<?php
/**
 * Modelo: categorías del menú general.
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/platillos.php';

function categorias_activas(): array
{
    return consultar('SELECT * FROM categorias WHERE activa = 1 ORDER BY orden, nombre');
}

/** Cada categoría activa con sus platillos disponibles anidados. */
function categorias_con_platillos(): array
{
    $categorias = categorias_activas();

    // Sin el binario de las fotos: la portada solo necesita saber que existen.
    $platillos = consultar(
        'SELECT ' . columnas_platillo() . ' FROM platillos WHERE activo = 1 ORDER BY orden, nombre'
    );

    $porCategoria = [];
    foreach ($platillos as $platillo) {
        $porCategoria[$platillo['categoria_id']][] = $platillo;
    }

    foreach ($categorias as &$categoria) {
        $categoria['platillos'] = $porCategoria[$categoria['id']] ?? [];
    }

    return $categorias;
}
