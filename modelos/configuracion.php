<?php
/**
 * Modelo: datos del negocio (tabla configuracion).
 * Son pares clave/valor que se leen en casi todas las páginas, por eso se
 * cachean en memoria durante la petición.
 */
require_once __DIR__ . '/../config/db.php';

/**
 * Claves editables desde el panel: clave => [etiqueta, tipo de campo].
 * Es la lista que arma el formulario y la que se acepta al guardar.
 */
function campos_del_negocio(): array
{
    return [
        'nombre_negocio' => ['Nombre del negocio', 'text'],
        'lema'           => ['Lema o frase corta', 'text'],
        'whatsapp'       => ['WhatsApp (con lada país, solo números)', 'text'],
        'telefono'       => ['Teléfono para mostrar', 'text'],
        'direccion'      => ['Dirección', 'text'],
        'maps_url'       => ['Enlace de Google Maps', 'url'],
        'horario'        => ['Horario de atención', 'text'],
        'aviso'          => ['Aviso en la portada (déjalo vacío para ocultarlo)', 'text'],
    ];
}

/** Todas las claves de configuración. Pasa true para releerlas de la base. */
function configuracion(bool $recargar = false): array
{
    static $valores = null;

    if ($valores === null || $recargar) {
        $valores = [];
        foreach (consultar('SELECT clave, valor FROM configuracion') as $fila) {
            $valores[$fila['clave']] = (string) $fila['valor'];
        }
    }

    return $valores;
}

/** Un valor suelto; cadena vacía si la clave no existe. */
function config(?string $clave = null)
{
    $valores = configuracion();

    if ($clave === null) {
        return $valores;
    }

    return $valores[$clave] ?? '';
}

/** Guarda varias claves de golpe y refresca el caché. */
function guardar_configuracion(array $valores): int
{
    $guardadas = 0;

    foreach ($valores as $clave => $valor) {
        $guardadas += ejecutar(
            'INSERT INTO configuracion (clave, valor) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor)',
            [$clave, $valor]
        ) > 0 ? 1 : 0;
    }

    configuracion(true);

    return $guardadas;
}
