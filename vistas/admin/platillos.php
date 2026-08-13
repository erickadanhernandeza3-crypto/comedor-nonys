<?php
/**
 * Pantalla "Menú general".
 * Espera: $platillos, $categorias, $editando
 */
vista('admin/encabezado', ['titulo' => 'Menú general', 'seccion' => 'platillos.php']);
?>

<div class="row g-4">
  <!-- ---------- Listado ---------- -->
  <div class="col-12 col-lg-7">
    <div class="panel" id="lista-platillos">
      <?php vista('admin/parciales/lista_platillos', ['platillos' => $platillos]); ?>
    </div>
  </div>

  <!-- ---------- Alta / edición ---------- -->
  <div class="col-12 col-lg-5">
    <div class="panel" id="formulario-platillo">
      <?php vista('admin/parciales/formulario_platillo', [
          'categorias' => $categorias,
          'editando'   => $editando,
      ]); ?>
    </div>
  </div>
</div>

<?php vista('admin/pie'); ?>
