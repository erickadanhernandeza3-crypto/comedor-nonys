<?php
/**
 * Menú general agrupado por categoría.
 * Espera: $categorias (cada una con su llave 'platillos')
 */
?>
<div class="titulo-seccion">
  <span class="icono">📖</span>
  <h2>Nuestro menú principal</h2>
</div>

<nav class="chips-categorias mb-4" aria-label="Categorías del menú">
  <?php foreach ($categorias as $categoria): ?>
    <a class="chip" href="#cat-<?= (int) $categoria['id'] ?>">
      <?= e($categoria['icono']) ?> <?= e($categoria['nombre']) ?>
    </a>
  <?php endforeach; ?>
</nav>

<?php foreach ($categorias as $categoria): ?>
  <div class="mb-5" id="cat-<?= (int) $categoria['id'] ?>">
    <div class="titulo-seccion">
      <span class="icono"><?= e($categoria['icono']) ?></span>
      <div>
        <h2><?= e($categoria['nombre']) ?></h2>
        <?php if ($categoria['descripcion']): ?>
          <p class="small text-muted mb-0"><?= e($categoria['descripcion']) ?></p>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!$categoria['platillos']): ?>
      <p class="text-muted">Pronto agregaremos platillos a esta sección.</p>
    <?php else: ?>
      <div class="row g-3 g-md-4">
        <?php foreach ($categoria['platillos'] as $platillo):
          $agotado = !$platillo['disponible'];
          $foto    = url_foto($platillo);
          $mensaje = sprintf(
              '¡Hola %s! Quiero pedir: %s (%s).',
              config('nombre_negocio'),
              $platillo['nombre'],
              precio((float) $platillo['precio'])
          );
        ?>
          <div class="col-12 col-sm-6 col-lg-4">
            <article class="tarjeta-platillo <?= $agotado ? 'tarjeta-platillo--agotado' : '' ?>">
              <?php if ($foto): ?>
                <img class="tarjeta-platillo__foto" src="<?= e($foto) ?>"
                     alt="<?= e($platillo['nombre']) ?>" loading="lazy">
              <?php else: ?>
                <div class="tarjeta-platillo__marco" aria-hidden="true"><?= e($categoria['icono']) ?></div>
              <?php endif; ?>

              <div class="tarjeta-platillo__cuerpo">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                  <h3 class="tarjeta-platillo__nombre"><?= e($platillo['nombre']) ?></h3>
                  <?php if ($platillo['destacado'] && !$agotado): ?>
                    <span class="etiqueta etiqueta--destacado">★ Recomendado</span>
                  <?php endif; ?>
                  <?php if ($agotado): ?>
                    <span class="etiqueta etiqueta--agotado">Se acabó</span>
                  <?php endif; ?>
                </div>

                <?php if ($platillo['descripcion']): ?>
                  <p class="tarjeta-platillo__desc"><?= e($platillo['descripcion']) ?></p>
                <?php endif; ?>

                <div class="tarjeta-platillo__pie">
                  <span class="precio"><?= precio((float) $platillo['precio']) ?></span>
                  <?php if ($agotado): ?>
                    <span class="small text-muted">No disponible hoy</span>
                  <?php else: ?>
                    <a class="btn btn-wa btn-sm px-3" target="_blank" rel="noopener"
                       href="<?= e(enlace_whatsapp($mensaje)) ?>">Pedir</a>
                  <?php endif; ?>
                </div>
              </div>
            </article>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
