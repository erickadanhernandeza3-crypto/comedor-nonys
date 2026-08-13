<?php
/**
 * Contenido del panel "Mi pedido".
 * Espera: $items (renglones ya con nombre y precio), $total
 */
if (!$items): ?>
  <div class="pedido-vacio">
    <div class="pedido-vacio__icono" aria-hidden="true">🧺</div>
    <p class="mb-1 fw-semibold">Tu pedido está vacío</p>
    <p class="text-muted small mb-0">
      Ve agregando platillos del menú y aquí se van juntando para mandarlos de una sola vez.
    </p>
  </div>
  <?php return; ?>
<?php endif; ?>

<div class="pedido-lista">
  <?php foreach ($items as $item): ?>
    <div class="pedido-renglon <?= $item['disponible'] ? '' : 'pedido-renglon--agotado' ?>">
      <div class="pedido-renglon__datos">
        <div class="pedido-renglon__nombre">
          <?= e($item['nombre']) ?>
          <?php if ($item['del_dia']): ?>
            <span class="etiqueta etiqueta--dia">del día</span>
          <?php endif; ?>
        </div>
        <div class="small text-muted">
          <?php if ($item['disponible']): ?>
            <?= precio($item['precio']) ?> c/u
          <?php else: ?>
            <span class="text-danger fw-semibold">Se acabó, quítalo para continuar</span>
          <?php endif; ?>
        </div>
      </div>

      <div class="pedido-renglon__cantidad">
        <button type="button" class="boton-redondo" aria-label="Quitar uno"
                data-pedido-cantidad data-tipo="<?= e($item['tipo']) ?>" data-id="<?= (int) $item['id'] ?>"
                data-cantidad="<?= $item['cantidad'] - 1 ?>">−</button>
        <span class="pedido-renglon__numero"><?= (int) $item['cantidad'] ?></span>
        <button type="button" class="boton-redondo" aria-label="Agregar uno"
                data-pedido-cantidad data-tipo="<?= e($item['tipo']) ?>" data-id="<?= (int) $item['id'] ?>"
                data-cantidad="<?= $item['cantidad'] + 1 ?>">+</button>
      </div>

      <div class="pedido-renglon__importe">
        <div class="precio"><?= precio($item['importe']) ?></div>
        <button type="button" class="btn btn-link btn-sm p-0 text-danger"
                data-pedido-quitar data-tipo="<?= e($item['tipo']) ?>" data-id="<?= (int) $item['id'] ?>">
          Quitar
        </button>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="pedido-total">
  <span>Total</span>
  <span class="precio precio--grande"><?= precio($total) ?></span>
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold" for="nota-pedido">
    Nota para la cocina <span class="text-muted fw-normal">(opcional)</span>
  </label>
  <input class="form-control" id="nota-pedido" data-pedido-nota maxlength="200"
         placeholder="Ej. sin cebolla, para llevar, a nombre de Erick">
</div>

<div class="d-grid gap-2">
  <button type="button" class="btn btn-wa btn-lg" data-pedido-enviar>
    Mandar el pedido por WhatsApp
  </button>
  <button type="button" class="btn btn-link btn-sm text-muted" data-pedido-vaciar>
    Vaciar el pedido
  </button>
</div>
