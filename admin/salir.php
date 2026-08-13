<?php
require_once __DIR__ . '/../includes/sesion.php';

cerrar_sesion();

header('Location: login.php');
