<?php
/**
 * Alta / edición de un platillo del menú general.
 * Espera: $categorias, $editando (fila de platillos o null)
 */
$esEdicion = $editando !== null;
$foto      = $esEdicion ? url_foto($editando, '../') : '';
?>
<h2 class="h6 text-uppercase text-muted mb-3">
  <?= $esEdicion ? 'Editar platillo' : 'Nuevo platillo' ?>
</h2>

<form data-ajax data-recurso="platillos" data-accion="guardar" enctype="multipart/form-data">
  <?php if ($esEdicion): ?>
    <input type="hidden" name="id" value="<?= (int) $editando['id'] ?>">
  <?php endif; ?>

  <div class="mb-3">
    <label class="form-label" for="categoria_id">Categoría</label>
    <select class="form-select" id="categoria_id" name="categoria_id" required>
      <option value="">Elige una…</option>
      <?php foreach ($categorias as $c): ?>
        <option value="<?= (int) $c['id'] ?>"
          <?= $esEdicion && (int) $editando['categoria_id'] === (int) $c['id'] ? 'selected' : '' ?>>
          <?= e($c['icono']) ?> <?= e($c['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label" for="nombre">Nombre</label>
    <input class="form-control" id="nombre" name="nombre" required
           value="<?= e($editando['nombre'] ?? '') ?>">
  </div>

  <div class="mb-3">
    <label class="form-label" for="descripcion">Descripción</label>
    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
              maxlength="400"><?= e($editando['descripcion'] ?? '') ?></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label" for="precio">Precio</label>
    <div class="input-group">
      <span class="input-group-text">$</span>
      <input class="form-control" id="precio" name="precio" type="number" step="0.50" min="0"
             value="<?= e(precio_campo((float) ($editando['precio'] ?? 0))) ?>">
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label" for="imagen_archivo">
      Foto del platillo <span class="text-muted fw-normal">(opcional)</span>
    </label>

    <div class="caja-foto mb-2" data-caja-foto>
      <img class="vista-previa <?= $foto ? '' : 'd-none' ?>"
           alt="Foto de <?= e($editando['nombre'] ?? 'el platillo') ?>"
           src="<?= e($foto) ?>" data-vista-previa>
      <p class="text-muted small mb-0 <?= $foto ? 'd-none' : '' ?>" data-sin-foto>
        Todavía no tiene foto. Se muestra un recuadro con el emoji de la categoría.
      </p>
    </div>

    <input class="form-control" type="file" id="imagen_archivo" name="imagen_archivo"
           accept="image/jpeg,image/png,image/gif,image/webp" data-elegir-foto>
    <div class="form-text">JPG, PNG, GIF o WEBP, hasta 5 MB. Se guarda dentro de la base de datos.</div>

    <?php if ($foto): ?>
      <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" id="quitar_imagen" name="quitar_imagen" value="1">
        <label class="form-check-label small" for="quitar_imagen">Quitar la foto actual</label>
      </div>
    <?php endif; ?>

    <!-- Alternativa a subir archivo: una liga de internet. -->
    <input class="form-control form-control-sm mt-2" id="imagen" name="imagen"
           value="<?= e($editando['imagen'] ?? '') ?>" placeholder="…o pega aquí una liga https://">
  </div>

  <div class="form-check form-switch mb-2">
    <input class="form-check-input" type="checkbox" role="switch" id="disponible" name="disponible"
           <?= !$esEdicion || $editando['disponible'] ? 'checked' : '' ?>>
    <label class="form-check-label" for="disponible">Disponible</label>
  </div>

  <div class="form-check form-switch mb-4">
    <input class="form-check-input" type="checkbox" role="switch" id="destacado" name="destacado"
           <?= $esEdicion && $editando['destacado'] ? 'checked' : '' ?>>
    <label class="form-check-label" for="destacado">Marcar como recomendado</label>
  </div>

  <div class="d-flex gap-2">
    <button class="btn btn-terracota flex-grow-1" type="submit">
      <?= $esEdicion ? 'Guardar cambios' : 'Agregar al menú' ?>
    </button>
    <?php if ($esEdicion): ?>
      <button class="btn btn-outline-secondary" type="button"
              data-accion-ajax data-recurso="platillos" data-accion="nuevo">Cancelar</button>
    <?php endif; ?>
  </div>
</form>
