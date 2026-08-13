<?php
/**
 * Pantalla "Datos del negocio".
 * Espera: $campos (clave => [etiqueta, tipo])
 */
vista('admin/encabezado', ['titulo' => 'Datos del negocio', 'seccion' => 'ajustes.php']);
?>

<div class="row g-4">
  <div class="col-12 col-lg-7">
    <div class="panel">
      <h1 class="h5 mb-3">Datos del negocio</h1>
      <p class="text-muted small">
        Todo esto se muestra en el menú público: encabezado, portada y pie de página.
      </p>

      <form data-ajax data-recurso="ajustes" data-accion="datos">
        <?php foreach ($campos as $clave => [$etiqueta, $tipo]): ?>
          <div class="mb-3">
            <label class="form-label" for="<?= e($clave) ?>"><?= e($etiqueta) ?></label>
            <input class="form-control" type="<?= e($tipo) ?>" id="<?= e($clave) ?>"
                   name="<?= e($clave) ?>" value="<?= e(config($clave)) ?>">
          </div>
        <?php endforeach; ?>

        <button class="btn btn-terracota" type="submit">Guardar datos</button>
      </form>
    </div>
  </div>

  <div class="col-12 col-lg-5">
    <div class="panel">
      <h2 class="h6 text-uppercase text-muted mb-3">Cambiar contraseña</h2>

      <form data-ajax data-recurso="ajustes" data-accion="contrasena" data-limpiar>
        <div class="mb-3">
          <label class="form-label" for="actual">Contraseña actual</label>
          <input class="form-control" type="password" id="actual" name="actual"
                 required autocomplete="current-password">
        </div>
        <div class="mb-3">
          <label class="form-label" for="nueva">Nueva contraseña</label>
          <input class="form-control" type="password" id="nueva" name="nueva"
                 required minlength="8" autocomplete="new-password">
        </div>
        <div class="mb-4">
          <label class="form-label" for="repetir">Repetir nueva contraseña</label>
          <input class="form-control" type="password" id="repetir" name="repetir"
                 required minlength="8" autocomplete="new-password">
        </div>

        <button class="btn btn-olivo w-100" type="submit">Cambiar contraseña</button>
      </form>
    </div>
  </div>
</div>

<?php vista('admin/pie'); ?>
