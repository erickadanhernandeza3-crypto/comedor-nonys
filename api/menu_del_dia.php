<?php
/**
 * Comida del día en JSON. Público, solo lectura.
 * GET api/menu_del_dia.php[?fecha=YYYY-MM-DD]
 */
require_once __DIR__ . '/../controladores/publico.php';

responder_json(menu_dia_publico($_GET));
