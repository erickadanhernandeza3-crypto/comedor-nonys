<?php
/**
 * Menú público. Punto de entrada: pide los datos al controlador y pinta la vista.
 */
require_once __DIR__ . '/controladores/publico.php';

vista('publico/inicio', pagina_inicio());
