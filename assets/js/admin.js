/*
 * Panel de Comedor Nony's.
 * Nada del panel recarga la página: todo se manda a admin/api.php y el servidor
 * responde con el aviso y los pedazos de HTML que hay que volver a pintar.
 *
 * En el HTML se marca así:
 *   <form data-ajax data-recurso="platillos" data-accion="guardar" [data-limpiar]>
 *   <button data-accion-ajax data-recurso="platillos" data-accion="eliminar" data-id="7">
 *   <input type="checkbox" data-disponible data-recurso="platillos" data-id="7">
 */

(function () {
  'use strict';

  const RUTA_API = 'api.php';
  const csrf     = document.querySelector('meta[name="csrf-token"]')?.content || '';

  /* ---------------- Llamada al router ---------------- */

  /** Manda recurso + acción + datos y devuelve siempre un objeto de respuesta. */
  async function enviar(recurso, accion, datos) {
    const cuerpo = datos instanceof FormData ? datos : new FormData();

    if (!(datos instanceof FormData)) {
      Object.entries(datos || {}).forEach(([clave, valor]) => cuerpo.set(clave, valor));
    }

    cuerpo.set('recurso', recurso);
    cuerpo.set('accion', accion);
    cuerpo.set('csrf', csrf);

    // La fecha que se está viendo acompaña a toda acción de la comida del día.
    const campoFecha = document.querySelector('[data-fecha-actual]');
    if (campoFecha && !cuerpo.has('fecha')) {
      cuerpo.set('fecha', campoFecha.value);
    }

    // Con foto se manda multipart (el navegador pone el Content-Type solo);
    // sin foto, texto plano, que es más ligero.
    const llevaFoto = [...cuerpo.values()].some((v) => v instanceof File && v.size > 0);

    if (!llevaFoto) {
      [...cuerpo.keys()].forEach((clave) => {
        if (cuerpo.get(clave) instanceof File) cuerpo.delete(clave);
      });
    }

    try {
      const respuesta = await fetch(RUTA_API, {
        method: 'POST',
        headers: { 'X-CSRF-Token': csrf },
        body: llevaFoto ? cuerpo : new URLSearchParams(cuerpo)
      });

      return await respuesta.json();
    } catch (error) {
      return respuestaLocal('No se pudo conectar con el servidor. Revisa tu conexión.');
    }
  }

  function respuestaLocal(mensaje) {
    return { ok: false, tipo: 'danger', mensaje: mensaje, fragmentos: {}, datos: {} };
  }

  /* ---------------- Pintar la respuesta ---------------- */

  function avisar(mensaje, tipo) {
    const avisos = document.querySelector('[data-avisos]');
    if (!mensaje || !avisos) return;

    const aviso = document.createElement('div');
    aviso.className = 'alert alert-' + (tipo || 'success') +
                      ' alert-dismissible fade show shadow-sm border-0';
    aviso.innerHTML = '<span></span>' +
      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>';
    aviso.querySelector('span').textContent = mensaje;

    avisos.appendChild(aviso);
    setTimeout(() => aviso.remove(), 6000);
  }

  /** Aplica avisos, fragmentos y datos sueltos. Devuelve si la acción salió bien. */
  function aplicar(respuesta) {
    avisar(respuesta.mensaje, respuesta.tipo);

    Object.entries(respuesta.fragmentos || {}).forEach(([selector, html]) => {
      const destino = document.querySelector(selector);
      if (destino) destino.innerHTML = html;
    });

    const datos = respuesta.datos || {};

    if (datos.fecha_larga) {
      const titulo = document.querySelector('[data-fecha-larga]');
      if (titulo) titulo.textContent = datos.fecha_larga;
    }

    if (datos.redirigir) {
      window.location.href = datos.redirigir;
    }

    return respuesta.ok === true;
  }

  function ocupado(elemento, esperando) {
    elemento.classList.toggle('cargando', esperando);
  }

  /* ---------------- Formularios ---------------- */

  document.addEventListener('submit', async (evento) => {
    const formulario = evento.target.closest('form[data-ajax]');
    if (!formulario) return;

    evento.preventDefault();
    ocupado(formulario, true);

    const respuesta = await enviar(
      formulario.dataset.recurso,
      formulario.dataset.accion,
      new FormData(formulario)
    );

    // El formulario pudo desaparecer si el servidor repintó su fragmento.
    if (formulario.isConnected) {
      ocupado(formulario, false);

      if (aplicar(respuesta) && formulario.hasAttribute('data-limpiar')) {
        formulario.reset();
      }
      return;
    }

    aplicar(respuesta);
  });

  /* ---------------- Botones sueltos (editar, eliminar, cancelar) ---------------- */

  document.addEventListener('click', async (evento) => {
    const boton = evento.target.closest('[data-accion-ajax]');
    if (!boton) return;

    if (boton.dataset.confirmar && !window.confirm(boton.dataset.confirmar)) return;

    boton.disabled = true;
    const respuesta = await enviar(boton.dataset.recurso, boton.dataset.accion, {
      id: boton.dataset.id || ''
    });
    if (boton.isConnected) boton.disabled = false;

    aplicar(respuesta);

    if (respuesta.ok && boton.dataset.irA) {
      document.querySelector(boton.dataset.irA)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });

  /* ---------------- Interruptor "Disponible / Se acabó" ---------------- */

  document.addEventListener('change', async (evento) => {
    const interruptor = evento.target.closest('[data-disponible]');
    if (!interruptor) return;

    // En la tabla del menú general la etiqueta vive en la celda; en la comida
    // del día, dentro del formulario del renglón.
    const contenedor = interruptor.closest('td') || interruptor.closest('form');
    const etiqueta   = contenedor?.querySelector('[data-etiqueta-disponible]');

    interruptor.disabled = true;
    const respuesta = await enviar(interruptor.dataset.recurso, 'disponibilidad', {
      id: interruptor.dataset.id,
      disponible: interruptor.checked ? '1' : '0'
    });
    interruptor.disabled = false;

    if (!respuesta.ok) {
      interruptor.checked = !interruptor.checked;
      avisar(respuesta.mensaje, 'danger');
      return;
    }

    if (etiqueta) {
      etiqueta.textContent = respuesta.datos.etiqueta;
      etiqueta.classList.toggle('etiqueta--disponible', respuesta.datos.disponible);
      etiqueta.classList.toggle('etiqueta--agotado', !respuesta.datos.disponible);
    }
  });

  /* ---------------- Cambio de fecha en la comida del día ---------------- */

  async function verFecha(fecha) {
    const campo = document.querySelector('[data-fecha-actual]');
    if (campo) campo.value = fecha;

    aplicar(await enviar('menu_dia', 'listar', { fecha: fecha }));
    history.replaceState(null, '', 'menu_dia.php?fecha=' + fecha);
  }

  document.addEventListener('change', (evento) => {
    const campo = evento.target.closest('[data-fecha-actual]');
    if (campo) verFecha(campo.value);
  });

  document.addEventListener('click', (evento) => {
    // toLocaleDateString('en-CA') da justo el formato AAAA-MM-DD que espera PHP.
    if (evento.target.closest('[data-fecha-hoy]')) {
      verFecha(new Date().toLocaleDateString('en-CA'));
    }
  });

  /* ---------------- Vista previa de la foto antes de guardarla ---------------- */

  document.addEventListener('change', (evento) => {
    const campo = evento.target.closest('[data-elegir-foto]');
    if (!campo) return;

    const caja    = campo.closest('.mb-3')?.querySelector('[data-caja-foto]');
    const previa  = caja?.querySelector('[data-vista-previa]');
    const sinFoto = caja?.querySelector('[data-sin-foto]');
    const archivo = campo.files[0];
    if (!previa || !archivo) return;

    previa.src = URL.createObjectURL(archivo);
    previa.classList.remove('d-none');
    sinFoto?.classList.add('d-none');
  });

  /* ---------------- Al elegir del catálogo, precargar el formulario ---------------- */

  document.addEventListener('change', (evento) => {
    const selector = evento.target.closest('[data-copiar-platillo]');
    if (!selector) return;

    const opcion = selector.selectedOptions[0];
    if (!opcion || !opcion.value) return;

    const formulario = selector.closest('form');
    formulario.nombre.value      = opcion.dataset.nombre || '';
    formulario.descripcion.value = opcion.dataset.descripcion || '';
    formulario.precio.value      = opcion.dataset.precio || '0.00';
  });
})();
