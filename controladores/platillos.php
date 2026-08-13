<?php
/**
 * Controlador: menú general (catálogo de platillos).
 */
require_once __DIR__ . '/../includes/sesion.php';

function controlador_platillos(string $accion, array $entrada): array
{
    switch ($accion) {
        case 'listar':         return estado_platillos();
        case 'guardar':        return platillos_guardar($entrada);
        case 'editar':         return platillos_editar($entrada);
        case 'nuevo':          return estado_platillos('', 'success', null);
        case 'eliminar':       return platillos_eliminar($entrada);
        case 'disponibilidad': return platillos_disponibilidad($entrada);
    }

    return respuesta_error('Acción no reconocida en el menú general.');
}

/** Respuesta estándar: aviso + listado + formulario (vacío o con el platillo a editar). */
function estado_platillos(string $mensaje = '', string $tipo = 'success', ?array $editando = null): array
{
    $platillos = platillos_con_categoria();

    return respuesta_ok($mensaje, [
        'tipo'       => $tipo,
        'fragmentos' => [
            '#lista-platillos' => vista_html('admin/parciales/lista_platillos', [
                'platillos' => $platillos,
            ]),
            '#formulario-platillo' => vista_html('admin/parciales/formulario_platillo', [
                'categorias' => categorias_activas(),
                'editando'   => $editando,
            ]),
        ],
        'datos' => ['total' => count($platillos)],
    ]);
}

function platillos_guardar(array $entrada): array
{
    $id     = entero($entrada, 'id');
    $nombre = texto($entrada, 'nombre');

    $datos = [
        'categoria_id' => entero($entrada, 'categoria_id'),
        'nombre'       => $nombre,
        'descripcion'  => texto($entrada, 'descripcion'),
        'precio'       => decimal($entrada, 'precio'),
        'imagen'       => texto($entrada, 'imagen'),
        'disponible'   => casilla($entrada, 'disponible'),
        'destacado'    => casilla($entrada, 'destacado'),
    ];

    if ($datos['nombre'] === '' || $datos['categoria_id'] === 0) {
        return respuesta_error('El nombre y la categoría son obligatorios.');
    }

    // La foto se revisa antes de tocar la base: si viene mal, no se guarda nada.
    $foto = revisar_imagen(archivo($entrada, 'imagen_archivo'));

    if ($foto['error'] !== '') {
        return respuesta_error($foto['error']);
    }

    if ($id) {
        if (!platillo($id)) {
            return respuesta_error('Ese platillo ya no existe en el menú.');
        }

        actualizar_platillo($id, $datos);

        if (!guardar_foto_del_formulario($id, $foto, casilla($entrada, 'quitar_imagen') === 1)) {
            return respuesta_error('Se guardaron los datos, pero la foto no cupo. Sube una más ligera.', 'warning');
        }

        return estado_platillos('Se actualizó «' . $nombre . '».');
    }

    $id = crear_platillo($datos);

    if (!guardar_foto_del_formulario($id, $foto, false)) {
        return respuesta_error('Se agregó el platillo, pero la foto no cupo. Súbela otra vez más ligera.', 'warning');
    }

    return estado_platillos('Se agregó «' . $nombre . '» al menú.');
}

/** Guarda la foto nueva, o borra la que había si marcaron la casilla. */
function guardar_foto_del_formulario(int $id, array $foto, bool $quitar): bool
{
    if ($foto['hay']) {
        return guardar_foto($id, $foto['tipo'], $foto['contenido']);
    }

    if ($quitar) {
        quitar_foto($id);
    }

    return true;
}

/** Carga un platillo en el formulario lateral, sin recargar la página. */
function platillos_editar(array $entrada): array
{
    $platillo = platillo(entero($entrada, 'id'));

    if (!$platillo) {
        return respuesta_error('Ese platillo ya no existe en el menú.');
    }

    return estado_platillos('', 'success', $platillo);
}

function platillos_eliminar(array $entrada): array
{
    $platillo = platillo(entero($entrada, 'id'));

    if (!$platillo) {
        return respuesta_error('Ese platillo ya no existe en el menú.');
    }

    // La foto vive en el mismo renglón, así que se va con él.
    eliminar_platillo((int) $platillo['id']);

    return estado_platillos('Se eliminó «' . $platillo['nombre'] . '» del menú.');
}

/** El interruptor "Disponible / Se acabó": solo cambia el renglón tocado. */
function platillos_disponibilidad(array $entrada): array
{
    $platillo = platillo(entero($entrada, 'id'));

    if (!$platillo) {
        return respuesta_error('Ese platillo ya no existe en el menú.');
    }

    $disponible = casilla($entrada, 'disponible') === 1;
    cambiar_disponible_platillo((int) $platillo['id'], $disponible);

    return respuesta_ok('', [
        'datos' => [
            'id'         => (int) $platillo['id'],
            'disponible' => $disponible,
            'etiqueta'   => $disponible ? 'Disponible' : 'Se acabó',
        ],
    ]);
}
