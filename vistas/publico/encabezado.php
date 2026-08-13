<?php
/**
 * Cabecera del menú público. Espera: $titulo
 */
$titulo = $titulo ?? config('nombre_negocio');
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?= e(config('nombre_negocio')) ?> — menú de comida tradicional mexicana y comida del día. <?= e(config('horario')) ?>">
<meta name="theme-color" content="#b8532c">
<title><?= e($titulo) ?></title>
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E%F0%9F%8D%B2%3C/text%3E%3C/svg%3E">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bitter:wght@600;700&family=Karla:wght@400;600;700&display=swap" rel="stylesheet">
<link href="assets/css/estilos.css" rel="stylesheet">
</head>
<body>

<div class="barra-superior">
  <div class="container d-flex flex-wrap justify-content-center justify-content-md-between gap-2 small">
    <span>🕑 <?= e(config('horario')) ?></span>
    <span class="d-none d-md-inline">
      📍 <a href="<?= e(config('maps_url')) ?>" target="_blank" rel="noopener"><?= e(config('direccion')) ?></a>
    </span>
  </div>
</div>

<nav class="navbar navbar-expand-md navbar-nonys sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <?= e(config('nombre_negocio')) ?>
      <small>Cocina tradicional mexicana</small>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navPrincipal" aria-controls="navPrincipal" aria-expanded="false" aria-label="Abrir menú">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navPrincipal">
      <ul class="navbar-nav ms-auto align-items-md-center gap-1">
        <li class="nav-item"><a class="nav-link" href="#comida-del-dia">Comida del día</a></li>
        <li class="nav-item"><a class="nav-link" href="#menu">Menú</a></li>
        <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
        <li class="nav-item ms-md-2">
          <a class="btn btn-wa btn-sm px-3" href="<?= e(enlace_whatsapp('¡Hola! Quiero hacer un pedido.')) ?>" target="_blank" rel="noopener">
            Pedir por WhatsApp
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="papel-picado"></div>
