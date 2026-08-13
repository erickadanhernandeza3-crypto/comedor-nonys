<?php
/**
 * Pantalla "Menú general". A partir de aquí todo se mueve por AJAX (api.php).
 */
require_once __DIR__ . '/../includes/sesion.php';
requerir_sesion();

vista('admin/platillos', [
    'platillos'  => platillos_con_categoria(),
    'categorias' => categorias_activas(),
    'editando'   => platillo(entero($_GET, 'editar')),
]);
