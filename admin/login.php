<?php
/**
 * Entrada al panel. El formulario se manda por AJAX al recurso "sesion".
 */
require_once __DIR__ . '/../includes/sesion.php';

if (usuario_actual()) {
    header('Location: menu_dia.php');
    exit;
}

vista('admin/login');
