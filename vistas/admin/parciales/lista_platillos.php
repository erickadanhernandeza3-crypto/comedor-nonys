<?php
/**
 * Tabla del menú general, agrupada por categoría.
 * Espera: $platillos (con las llaves 'categoria' e 'icono')
 */
?>
<h1 class="h5 mb-3">
  Menú general
  <span class="text-muted fw-normal">(<?= count($platillos) ?> platillos)</span>
</h1>

<div class="table-responsive">
  <table class="table tabla-admin align-middle">
    <thead>
      <tr class="small text-uppercase text-muted">
        <th>Platillo</th>
        <th class="text-end">Precio</th>
        <th class="text-center">Estado</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php $categoriaPrevia = null; ?>
      <?php foreach ($platillos as $p): ?>
        <?php if ($p['categoria'] !== $categoriaPrevia): $categoriaPrevia = $p['categoria']; ?>
          <tr class="table-light">
            <td colspan="4" class="fw-bold small text-uppercase">
              <?= e($p['icono']) ?> <?= e($p['categoria']) ?>
            </td>
          </tr>
        <?php endif; ?>
        <tr>
          <td>
            <?php $foto = url_foto($p, '../'); ?>
            <div class="d-flex align-items-center gap-2">
              <?php if ($foto): ?>
                <img class="miniatura" alt="" src="<?= e($foto) ?>">
              <?php endif; ?>
              <div>
                <div class="fw-semibold">
                  <?= e($p['nombre']) ?>
                  <?php if ($p['destacado']): ?>
                    <span class="etiqueta etiqueta--destacado">★</span>
                  <?php endif; ?>
                </div>
                <div class="small text-muted">
                  <?= e(mb_strimwidth((string) $p['descripcion'], 0, 70, '…')) ?>
                </div>
              </div>
            </div>
          </td>
          <td class="text-end fw-semibold"><?= precio((float) $p['precio']) ?></td>
          <td class="text-center">
            <div class="form-check form-switch d-inline-block">
              <input class="form-check-input" type="checkbox" role="switch"
                     data-disponible data-recurso="platillos" data-id="<?= (int) $p['id'] ?>"
                     aria-label="Disponibilidad de <?= e($p['nombre']) ?>"
                     <?= $p['disponible'] ? 'checked' : '' ?>>
            </div>
            <div class="etiqueta <?= $p['disponible'] ? 'etiqueta--disponible' : 'etiqueta--agotado' ?>"
                 data-etiqueta-disponible>
              <?= $p['disponible'] ? 'Disponible' : 'Se acabó' ?>
            </div>
          </td>
          <td class="text-end text-nowrap">
            <button class="btn btn-sm btn-outline-secondary" type="button"
                    data-accion-ajax data-recurso="platillos" data-accion="editar"
                    data-id="<?= (int) $p['id'] ?>" data-ir-a="#formulario-platillo">Editar</button>
            <button class="btn btn-sm btn-outline-danger" type="button"
                    data-accion-ajax data-recurso="platillos" data-accion="eliminar"
                    data-id="<?= (int) $p['id'] ?>"
                    data-confirmar="¿Eliminar «<?= e($p['nombre']) ?>» del menú?">×</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
