/* Comedor Nony's — refresco de la comida del día sin recargar la página */

(function () {
  'use strict';

  const bloque = document.querySelector('[data-menu-dia]');
  if (!bloque) return;

  const lista  = bloque.querySelector('[data-menu-dia-lista]');
  const fecha  = bloque.querySelector('[data-menu-dia-fecha]');
  const boton  = bloque.querySelector('[data-menu-dia-refrescar]');
  const sello  = document.querySelector('[data-menu-dia-sello]');

  let cargando = false;

  async function refrescar(manual) {
    if (cargando) return;
    cargando = true;
    lista.classList.add('cargando');

    try {
      const respuesta = await fetch('api/menu_del_dia.php', {
        headers: { 'Accept': 'application/json' },
        cache: 'no-store'
      });
      if (!respuesta.ok) throw new Error('HTTP ' + respuesta.status);

      const datos = await respuesta.json();
      lista.innerHTML = datos.html;
      fecha.textContent = datos.fecha_larga;

      if (sello) {
        sello.textContent = 'Actualizado a las ' + datos.actualizado +
          ' · la disponibilidad se revisa sola cada minuto.';
      }
    } catch (error) {
      if (manual && sello) {
        sello.textContent = 'No pudimos actualizar. Revisa tu conexión e inténtalo de nuevo.';
      }
    } finally {
      lista.classList.remove('cargando');
      cargando = false;
    }
  }

  if (boton) {
    boton.addEventListener('click', () => refrescar(true));
  }

  setInterval(() => refrescar(false), 60000);

  // Al volver a la pestaña, traer el estado más reciente.
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') refrescar(false);
  });

  // Desplazamiento suave hacia las categorías compensando la barra fija.
  document.querySelectorAll('a[href^="#"]').forEach((enlace) => {
    enlace.addEventListener('click', (evento) => {
      const destino = document.querySelector(enlace.getAttribute('href'));
      if (!destino) return;
      evento.preventDefault();
      const desplazamiento = destino.getBoundingClientRect().top + window.scrollY - 80;
      window.scrollTo({ top: desplazamiento, behavior: 'smooth' });
    });
  });
})();
