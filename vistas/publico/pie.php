<footer class="pie" id="contacto">
  <div class="container">
    <div class="row g-4">
      <div class="col-12 col-md-4">
        <h5><?= e(config('nombre_negocio')) ?></h5>
        <p class="small mb-0"><?= e(config('lema')) ?></p>
      </div>
      <div class="col-6 col-md-4">
        <h5>Horario</h5>
        <p class="small mb-0"><?= e(config('horario')) ?></p>
      </div>
      <div class="col-6 col-md-4">
        <h5>Encuéntranos</h5>
        <p class="small mb-1">
          <a href="<?= e(config('maps_url')) ?>" target="_blank" rel="noopener">
            <?= e(config('direccion')) ?>
          </a>
        </p>
        <p class="small mb-0">
          <a href="tel:<?= e(preg_replace('/\D/', '', config('telefono'))) ?>"><?= e(config('telefono')) ?></a>
        </p>
      </div>
    </div>
    <hr class="my-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 small">
      <span>&copy; <?= date('Y') ?> <?= e(config('nombre_negocio')) ?>. Todos los derechos reservados.</span>
      <a href="admin/index.php">Panel administrativo</a>
    </div>
  </div>
</footer>

<a class="wa-flotante d-md-none" href="<?= e(enlace_whatsapp('¡Hola! Quiero hacer un pedido.')) ?>"
   target="_blank" rel="noopener" aria-label="Pedir por WhatsApp">
  <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm0 18.15h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.19 8.19 0 0 1-1.26-4.38c0-4.54 3.7-8.23 8.25-8.23a8.23 8.23 0 0 1 8.24 8.24c0 4.54-3.7 8.23-8.24 8.23zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.24-.64.8-.79.97-.14.16-.29.18-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.43.12-.15.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.42-.56-.43h-.48c-.16 0-.43.06-.65.31-.22.25-.85.83-.85 2.02s.87 2.34 1 2.51c.12.16 1.71 2.61 4.15 3.66.58.25 1.03.4 1.38.51.58.19 1.11.16 1.53.1.47-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.11-.22-.17-.47-.29z"/>
  </svg>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
