<?php
/**
 * Marco del panel: barra, pestañas y zona de avisos del AJAX.
 * Espera: $titulo, $seccion (archivo activo)
 */
$titulo   = $titulo ?? 'Panel';
$seccion  = $seccion ?? '';
$usuario  = usuario_actual();

$secciones = [
    'menu_dia.php'  => ['Comida del día',    '🍲'],
    'platillos.php' => ['Menú general',      '📖'],
    'ajustes.php'   => ['Datos del negocio', '⚙️'],
];
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<meta name="csrf-token" content="<?= e(token_csrf()) ?>">
<title><?= e($titulo) ?> — <?= e(config('nombre_negocio')) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Bitter:wght@600;700&family=Karla:wght@400;600;700&display=swap" rel="stylesheet">
<link href="../assets/css/estilos.css" rel="stylesheet">
</head>
<body class="admin-cuerpo">

<div class="admin-barra">
  <div class="container d-flex flex-wrap align-items-center gap-3">
    <a href="menu_dia.php" class="fw-bold me-auto">
      <?= e(config('nombre_negocio')) ?> · Administración
    </a>
    <a href="../index.php" target="_blank" class="small">Ver el menú público ↗</a>
    <span class="small text-white-50"><?= e($usuario['nombre'] ?? '') ?></span>
    <a href="salir.php" class="small">Salir</a>
  </div>
</div>

<!-- Aquí aparecen los avisos que devuelve cada llamada AJAX. -->
<div class="admin-avisos" data-avisos></div>

<div class="container py-4">
  <ul class="nav nav-pills gap-2 mb-4">
    <?php foreach ($secciones as $archivo => [$etiqueta, $icono]): ?>
      <li class="nav-item">
        <a class="nav-link <?= $seccion === $archivo ? 'active' : 'text-dark bg-white border' ?>"
           href="<?= e($archivo) ?>"><?= $icono ?> <?= e($etiqueta) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
