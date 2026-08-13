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

  /* ================= Mi pedido =================
   * El pedido lo lleva el servidor en la sesión; aquí solo se mandan las
   * acciones y se vuelve a pintar el panel con lo que responde.
   */

  const panel  = document.getElementById('panel-pedido');
  const barra  = document.querySelector('[data-barra-pedido]');
  if (!panel) return;

  async function pedir(datos) {
    try {
      const respuesta = await fetch('api/pedido.php', {
        method: 'POST',
        body: new URLSearchParams(datos)
      });

      return await respuesta.json();
    } catch (error) {
      return { ok: false, tipo: 'danger', mensaje: 'No se pudo conectar. Revisa tu internet.',
               fragmentos: {}, datos: {} };
    }
  }

  /** Repinta el panel, la barra de abajo y avisa lo que pasó. */
  function aplicar(respuesta) {
    Object.entries(respuesta.fragmentos || {}).forEach(([selector, html]) => {
      const destino = document.querySelector(selector);
      if (destino) destino.innerHTML = html;
    });

    const datos = respuesta.datos || {};

    if (barra && datos.piezas !== undefined) {
      barra.classList.toggle('d-none', datos.piezas === 0);
      barra.querySelector('[data-pedido-piezas]').textContent = datos.piezas;
      barra.querySelector('[data-pedido-total]').textContent = datos.texto_total;
      document.body.classList.toggle('con-pedido', datos.piezas > 0);
    }

    if (respuesta.mensaje) avisar(respuesta.mensaje, respuesta.tipo);

    return respuesta;
  }

  /** Globito de aviso, arriba a la derecha. */
  function avisar(mensaje, tipo) {
    document.querySelector('.aviso-flotante')?.remove();

    const aviso = document.createElement('div');
    aviso.className = 'aviso-flotante aviso-flotante--' + (tipo || 'success');
    aviso.textContent = mensaje;
    document.body.appendChild(aviso);

    setTimeout(() => aviso.remove(), 3500);
  }

  // Botones "+ Agregar" del menú, incluidos los que llegan al refrescar el día.
  document.addEventListener('click', async (evento) => {
    const boton = evento.target.closest('[data-agregar-pedido]');
    if (!boton) return;

    boton.disabled = true;
    aplicar(await pedir({ accion: 'agregar', tipo: boton.dataset.tipo, id: boton.dataset.id }));
    boton.disabled = false;
  });

  // Cantidades, quitar y vaciar, dentro del panel.
  document.addEventListener('click', async (evento) => {
    const cantidad = evento.target.closest('[data-pedido-cantidad]');
    if (cantidad) {
      aplicar(await pedir({ accion: 'cantidad', tipo: cantidad.dataset.tipo,
                            id: cantidad.dataset.id, cantidad: cantidad.dataset.cantidad }));
      return;
    }

    const quitar = evento.target.closest('[data-pedido-quitar]');
    if (quitar) {
      aplicar(await pedir({ accion: 'quitar', tipo: quitar.dataset.tipo, id: quitar.dataset.id }));
      return;
    }

    const vaciar = evento.target.closest('[data-pedido-vaciar]');
    if (vaciar && window.confirm('¿Vaciar todo tu pedido?')) {
      aplicar(await pedir({ accion: 'vaciar' }));
      return;
    }

    const enviar = evento.target.closest('[data-pedido-enviar]');
    if (enviar) {
      enviar.disabled = true;
      const respuesta = aplicar(await pedir({
        accion: 'enviar',
        nota: document.querySelector('[data-pedido-nota]')?.value || ''
      }));
      enviar.disabled = false;

      // Mismo tab: así ningún bloqueador de ventanas emergentes lo detiene.
      if (respuesta.ok && respuesta.datos.whatsapp) {
        window.location.href = respuesta.datos.whatsapp;
      }
    }
  });
})();
