<?php
require_once __DIR__ . '/../includes/sesion.php';

header('Location: ' . (usuario_actual() ? 'menu_dia.php' : 'login.php'));
