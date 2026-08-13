<?php
/**
 * Pantalla "Comida del día".
 * Espera: $fecha, $items, $catalogo
 */
vista('admin/encabezado', ['titulo' => 'Comida del día', 'seccion' => 'menu_dia.php']);
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
  <div>
    <h1 class="h4 mb-1">Comida del día</h1>
    <p class="text-muted mb-0" data-fecha-larga><?= e(fecha_larga($fecha)) ?></p>
  </div>

  <div class="d-flex align-items-end gap-2">
    <div>
      <label class="form-label mb-1" for="fecha">Ver otro día</label>
      <input type="date" class="form-control" id="fecha" value="<?= e($fecha) ?>" data-fecha-actual>
    </div>
    <button class="btn btn-outline-secondary" type="button" data-fecha-hoy>Hoy</button>
  </div>
</div>

<div class="row g-4">
  <!-- ---------- Platillos ya capturados ---------- -->
  <div class="col-12 col-lg-7">
    <div class="panel" id="lista-menu-dia">
      <?php vista('admin/parciales/lista_menu_dia', ['items' => $items, 'fecha' => $fecha]); ?>
    </div>
  </div>

  <!-- ---------- Alta y copia ---------- -->
  <div class="col-12 col-lg-5">
    <div class="panel mb-4">
      <h2 class="h6 text-uppercase text-muted mb-3">Agregar platillo</h2>

      <form data-ajax data-recurso="menu_dia" data-accion="agregar" data-limpiar>
        <div class="mb-3">
          <label class="form-label" for="platillo_id">
            Tomar del menú general <span class="text-muted fw-normal">(opcional)</span>
          </label>
          <select class="form-select" id="platillo_id" name="platillo_id" data-copiar-platillo>
            <option value="">— Escribirlo a mano —</option>
            <?php foreach ($catalogo as $p): ?>
              <option value="<?= (int) $p['id'] ?>"
                      data-nombre="<?= e($p['nombre']) ?>"
                      data-descripcion="<?= e($p['descripcion']) ?>"
                      data-precio="<?= e(precio_campo((float) $p['precio'])) ?>">
                <?= e($p['categoria']) ?> · <?= e($p['nombre']) ?> — <?= precio((float) $p['precio']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label" for="nombre">Nombre</label>
          <input class="form-control" id="nombre" name="nombre" required
                 placeholder="Ej. Milanesa de res empanizada">
        </div>

        <div class="mb-3">
          <label class="form-label" for="descripcion">Descripción</label>
          <textarea class="form-control" id="descripcion" name="descripcion" rows="2"
                    placeholder="Ej. Con arroz, ensalada y frijoles de la olla."></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label" for="precio">Precio</label>
          <div class="input-group">
            <span class="input-group-text">$</span>
            <input class="form-control" id="precio" name="precio" type="number" step="0.50" min="0" value="0.00">
          </div>
          <div class="form-text">Déjalo en 0 si va incluido en la comida corrida.</div>
        </div>

        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" role="switch" id="disponible" name="disponible" checked>
          <label class="form-check-label" for="disponible">Disponible</label>
        </div>

        <button class="btn btn-terracota w-100" type="submit">Agregar al menú del día</button>
      </form>
    </div>

    <div class="panel">
      <h2 class="h6 text-uppercase text-muted mb-3">Copiar de otro día</h2>

      <form class="d-flex flex-wrap gap-2 align-items-end"
            data-ajax data-recurso="menu_dia" data-accion="copiar">
        <div class="flex-grow-1">
          <label class="form-label" for="fecha_origen">Traer el menú del</label>
          <input type="date" class="form-control" id="fecha_origen" name="fecha_origen"
                 value="<?= e(date('Y-m-d', strtotime($fecha . ' -7 days'))) ?>" required>
        </div>
        <button class="btn btn-outline-secondary" type="submit">Copiar</button>
      </form>

      <p class="form-text mb-0">
        Los platillos se agregan a los que ya tienes, marcados como disponibles.
      </p>
    </div>
  </div>
</div>

<?php vista('admin/pie'); ?>
