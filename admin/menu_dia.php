<?php
/**
 * Pantalla "Comida del día". A partir de aquí todo se mueve por AJAX (api.php).
 */
require_once __DIR__ . '/../includes/sesion.php';
requerir_sesion();

$fecha = fecha($_GET);

vista('admin/menu_dia', [
    'fecha'    => $fecha,
    'items'    => menu_del_dia($fecha),
    'catalogo' => platillos_para_elegir(),
]);
