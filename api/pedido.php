<?php
/**
 * Router del pedido del cliente. Público: no hay cuentas ni contraseñas, el
 * pedido vive en la sesión del navegador de quien está viendo el menú.
 *
 * POST api/pedido.php { accion, tipo, id, cantidad, nota }
 */
require_once __DIR__ . '/../controladores/pedido.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_json(respuesta_error('Esta dirección solo atiende peticiones POST.'), 405);
}

responder_json(controlador_pedido(texto($_POST, 'accion'), $_POST));
