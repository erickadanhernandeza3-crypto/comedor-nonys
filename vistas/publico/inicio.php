<?php
/**
 * Portada del menú público.
 * Espera: $titulo, $hoy, $items (comida del día), $categorias (con platillos),
 *         $pedido (renglones del pedido), $totalPedido
 */
vista('publico/encabezado', ['titulo' => $titulo]);
?>

<header class="portada">
  <div class="container">
    <h1><?= e(config('nombre_negocio')) ?></h1>
    <p class="lead mb-3"><?= e(config('lema')) ?></p>
    <div class="d-flex flex-wrap justify-content-center gap-2">
      <span class="pastilla-info">🕑 <?= e(config('horario')) ?></span>
      <a class="pastilla-info" href="<?= e(config('maps_url')) ?>" target="_blank" rel="noopener">
        📍 Cómo llegar
      </a>
    </div>
  </div>
</header>

<?php if (config('aviso')): ?>
  <div class="container mt-3">
    <div class="alert alert-warning border-0 shadow-sm mb-0">
      <strong>Aviso:</strong> <?= e(config('aviso')) ?>
    </div>
  </div>
<?php endif; ?>

<main class="container py-4">

  <!-- ================= Comida del día ================= -->
  <section id="comida-del-dia" class="mb-5">
    <div class="bloque-dia" data-menu-dia>
      <div class="bloque-dia__cabecera d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
          <h2>Comida del día</h2>
          <div class="bloque-dia__fecha" data-menu-dia-fecha><?= e(fecha_larga($hoy)) ?></div>
        </div>
        <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-menu-dia-refrescar>
          ↻ Actualizar
        </button>
      </div>
      <div class="bloque-dia__cuerpo" data-menu-dia-lista>
        <?php vista('publico/parciales/menu_dia', ['items' => $items]); ?>
      </div>
    </div>
    <p class="small text-muted mt-2 mb-0" data-menu-dia-sello>
      La disponibilidad se actualiza sola cada minuto.
    </p>
  </section>

  <!-- ================= Menú general ================= -->
  <section id="menu">
    <?php vista('publico/parciales/menu_general', ['categorias' => $categorias]); ?>
  </section>

  <!-- ================= Visítanos ================= -->
  <section class="panel">
    <div class="row g-4 align-items-center">
      <div class="col-12 col-md-7">
        <div class="titulo-seccion">
          <span class="icono">🏠</span>
          <h2>Visítanos</h2>
        </div>
        <p class="mb-2"><strong>Dirección:</strong> <?= e(config('direccion')) ?></p>
        <p class="mb-2"><strong>Horario:</strong> <?= e(config('horario')) ?></p>
        <p class="mb-0"><strong>Teléfono:</strong>
          <a href="tel:<?= e(preg_replace('/\D/', '', config('telefono'))) ?>"><?= e(config('telefono')) ?></a>
        </p>
      </div>
      <div class="col-12 col-md-5 d-grid gap-2">
        <a class="btn btn-terracota btn-lg" href="<?= e(config('maps_url')) ?>" target="_blank" rel="noopener">
          📍 Ver en Google Maps
        </a>
        <a class="btn btn-wa btn-lg" target="_blank" rel="noopener"
           href="<?= e(enlace_whatsapp('¡Hola! Me gustaría hacer un pedido.')) ?>">
          Pedir por WhatsApp
        </a>
      </div>
    </div>
  </section>

</main>

<!-- ================= Mi pedido ================= -->
<aside class="offcanvas offcanvas-end panel-pedido" tabindex="-1" id="miPedido"
       aria-labelledby="tituloPedido">
  <div class="offcanvas-header">
    <h2 class="offcanvas-title h5 mb-0" id="tituloPedido">🧺 Mi pedido</h2>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
  </div>
  <div class="offcanvas-body" id="panel-pedido">
    <?php vista('publico/parciales/pedido', ['items' => $pedido, 'total' => $totalPedido]); ?>
  </div>
</aside>

<!-- Barra que aparece abajo en cuanto hay algo pedido -->
<div class="barra-pedido <?= $pedido ? '' : 'd-none' ?>" data-barra-pedido>
  <button type="button" class="barra-pedido__boton" data-bs-toggle="offcanvas" data-bs-target="#miPedido">
    <span class="barra-pedido__cuenta" data-pedido-piezas><?= piezas_pedido($pedido) ?></span>
    <span>Ver mi pedido</span>
    <span class="barra-pedido__total" data-pedido-total><?= precio($totalPedido) ?></span>
  </button>
</div>

<?php vista('publico/pie'); ?>
