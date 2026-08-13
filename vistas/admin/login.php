<?php
/**
 * Entrada al panel. El formulario también viaja por AJAX.
 */
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<meta name="csrf-token" content="<?= e(token_csrf()) ?>">
<title>Entrar — <?= e(config('nombre_negocio')) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Bitter:wght@600;700&family=Karla:wght@400;600;700&display=swap" rel="stylesheet">
<link href="../assets/css/estilos.css" rel="stylesheet">
</head>
<body class="admin-cuerpo">

<div class="admin-avisos" data-avisos></div>

<div class="container" style="max-width: 26rem;">
  <div class="text-center py-5">
    <h1 class="h3 mb-1"><?= e(config('nombre_negocio')) ?></h1>
    <p class="text-muted mb-0">Panel administrativo</p>
  </div>

  <form class="panel" data-ajax data-recurso="sesion" data-accion="entrar">
    <div class="mb-3">
      <label class="form-label" for="usuario">Usuario</label>
      <input class="form-control" id="usuario" name="usuario" required autofocus
             autocomplete="username">
    </div>

    <div class="mb-4">
      <label class="form-label" for="contrasena">Contraseña</label>
      <input class="form-control" id="contrasena" name="contrasena" type="password"
             required autocomplete="current-password">
    </div>

    <button class="btn btn-terracota w-100 py-2" type="submit">Entrar</button>
  </form>

  <p class="text-center small text-muted mt-3">
    <a href="../index.php">← Volver al menú</a>
  </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/admin.js"></script>
</body>
</html>
