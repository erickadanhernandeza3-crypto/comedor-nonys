<?php
/**
 * Lista de platillos de la comida del día.
 * Es la única fuente del marcado: la portada la imprime al cargar y
 * api/menu_del_dia.php la devuelve para que el AJAX reemplace el bloque.
 *
 * Espera: $items (filas de menu_del_dia)
 */
if (!$items) {
    echo '<p class="text-muted mb-0">Todavía no publicamos el menú de hoy. '
       . 'Escríbenos por WhatsApp y con gusto te decimos qué hay en la cazuela.</p>';
    return;
}

$numero = 0;
foreach ($items as $item):
    $numero++;
    $agotado = !$item['disponible'];
    $mensaje = sprintf(
        '¡Hola %s! Quiero pedir de la comida del día: %s.',
        config('nombre_negocio'),
        $item['nombre']
    );
?>
  <div class="platillo-dia <?= $agotado ? 'platillo-dia--agotado' : '' ?>">
    <span class="platillo-dia__num"><?= $numero ?></span>
    <div class="platillo-dia__cuerpo">
      <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="platillo-dia__nombre"><?= e($item['nombre']) ?></span>
        <?php if ($agotado): ?>
          <span class="etiqueta etiqueta--agotado">Se acabó</span>
        <?php else: ?>
          <span class="etiqueta etiqueta--disponible">Disponible</span>
        <?php endif; ?>
      </div>
      <?php if ($item['descripcion']): ?>
        <p class="platillo-dia__desc"><?= e($item['descripcion']) ?></p>
      <?php endif; ?>
    </div>
    <div class="text-end">
      <?php if ((float) $item['precio'] > 0): ?>
        <div class="precio"><?= precio((float) $item['precio']) ?></div>
      <?php else: ?>
        <div class="small text-muted">Incluido</div>
      <?php endif; ?>
      <?php if (!$agotado): ?>
        <a class="btn btn-wa btn-sm mt-1 px-3" target="_blank" rel="noopener"
           href="<?= e(enlace_whatsapp($mensaje)) ?>">Pedir</a>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
