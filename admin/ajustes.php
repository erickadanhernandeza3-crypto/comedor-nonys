<?php
/**
 * Pantalla "Datos del negocio". A partir de aquí todo se mueve por AJAX (api.php).
 */
require_once __DIR__ . '/../includes/sesion.php';
requerir_sesion();

vista('admin/ajustes', ['campos' => campos_del_negocio()]);
