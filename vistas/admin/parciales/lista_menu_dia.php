<?php
/**
 * Platillos capturados en una fecha, cada uno en su formulario de edición.
 * Espera: $items, $fecha
 */
?>
<h2 class="h6 text-uppercase text-muted mb-3">
  Platillos de este día (<?= count($items) ?>)
</h2>

<?php if (!$items): ?>
  <p class="text-muted mb-0">
    Aún no hay nada capturado para esta fecha. Agrégalo en el formulario de al lado.
  </p>
  <?php return; ?>
<?php endif; ?>

<?php foreach ($items as $item): ?>
  <form class="border rounded-3 p-3 mb-3" data-ajax data-recurso="menu_dia" data-accion="editar">
    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">

    <div class="row g-2 align-items-end">
      <div class="col-12 col-sm-7">
        <label class="form-label" for="nombre<?= (int) $item['id'] ?>">Nombre</label>
        <input class="form-control" id="nombre<?= (int) $item['id'] ?>" name="nombre"
               value="<?= e($item['nombre']) ?>" required>
      </div>
      <div class="col-6 col-sm-5">
        <label class="form-label" for="precio<?= (int) $item['id'] ?>">Precio</label>
        <div class="input-group">
          <span class="input-group-text">$</span>
          <input class="form-control" id="precio<?= (int) $item['id'] ?>" name="precio"
                 type="number" step="0.50" min="0"
                 value="<?= e(precio_campo((float) $item['precio'])) ?>">
        </div>
      </div>
      <div class="col-12">
        <label class="form-label" for="descripcion<?= (int) $item['id'] ?>">Descripción</label>
        <input class="form-control" id="descripcion<?= (int) $item['id'] ?>" name="descripcion"
               value="<?= e($item['descripcion']) ?>"
               placeholder="Opcional. Ej. con arroz, frijoles y tortillas.">
      </div>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch"
               id="disponible<?= (int) $item['id'] ?>" name="disponible"
               data-disponible data-recurso="menu_dia" data-id="<?= (int) $item['id'] ?>"
               <?= $item['disponible'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="disponible<?= (int) $item['id'] ?>">
          <span class="etiqueta <?= $item['disponible'] ? 'etiqueta--disponible' : 'etiqueta--agotado' ?>"
                data-etiqueta-disponible>
            <?= $item['disponible'] ? 'Disponible' : 'Se acabó' ?>
          </span>
        </label>
      </div>
      <div class="ms-auto d-flex gap-2">
        <button class="btn btn-sm btn-olivo" type="submit">Guardar</button>
        <button class="btn btn-sm btn-outline-danger" type="button"
                data-accion-ajax data-recurso="menu_dia" data-accion="eliminar"
                data-id="<?= (int) $item['id'] ?>"
                data-confirmar="¿Quitar «<?= e($item['nombre']) ?>» del menú del día?">Quitar</button>
      </div>
    </div>
  </form>
<?php endforeach; ?>
