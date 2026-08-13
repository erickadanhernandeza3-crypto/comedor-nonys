<?php
/**
 * Revisión de las fotos que llegan del formulario.
 *
 * Aquí solo se valida y se lee el archivo; quien lo guarda es el modelo, que lo
 * mete en la base. Así la foto sobrevive en servidores de disco efímero (Render)
 * y no quedan archivos huérfanos en el proyecto.
 */

/** Peso máximo que aceptamos, en bytes. Una foto de celular ronda los 3 MB. */
const PESO_MAXIMO_IMAGEN = 5242880;

/**
 * Límite real de esta instalación.
 * La foto viaja a MySQL en un solo mensaje, así que no puede pasar de
 * max_allowed_packet: si el servidor lo tiene bajo, mandamos avisar con el
 * número verdadero en lugar de reventar a media subida.
 */
function limite_foto(): int
{
    static $limite = null;

    if ($limite === null) {
        $fila   = consultar_una('SELECT @@max_allowed_packet AS paquete');
        $margen = (int) (((int) ($fila['paquete'] ?? 0)) * 0.9);
        $limite = $margen > 0 ? min(PESO_MAXIMO_IMAGEN, $margen) : PESO_MAXIMO_IMAGEN;
    }

    return $limite;
}

/** "5 MB", "900 KB": el límite dicho como lo entiende una persona. */
function limite_foto_legible(): string
{
    $limite = limite_foto();

    return $limite >= 1048576
        ? round($limite / 1048576, 1) . ' MB'
        : round($limite / 1024) . ' KB';
}

/** Tipos permitidos: tipo detectado => tipo que se guarda en la base. */
function tipos_de_imagen(): array
{
    return [
        IMAGETYPE_JPEG => 'image/jpeg',
        IMAGETYPE_PNG  => 'image/png',
        IMAGETYPE_GIF  => 'image/gif',
        IMAGETYPE_WEBP => 'image/webp',
    ];
}

/**
 * Revisa la foto que llegó en el formulario y la deja lista para guardarse.
 *
 * @param array|null $archivo Un renglón de $_FILES, o null si no mandaron nada.
 * @return array ['hay' => bool, 'tipo' => string, 'contenido' => string, 'error' => string]
 */
function revisar_imagen(?array $archivo): array
{
    $vacio = ['hay' => false, 'tipo' => '', 'contenido' => '', 'error' => ''];
    $error = static fn(string $texto): array =>
        ['hay' => false, 'tipo' => '', 'contenido' => '', 'error' => $texto];

    $codigo = $archivo['error'] ?? UPLOAD_ERR_NO_FILE;

    if (!$archivo || $codigo === UPLOAD_ERR_NO_FILE || ($archivo['size'] ?? 0) === 0) {
        return $vacio;
    }

    if ($codigo === UPLOAD_ERR_INI_SIZE || $codigo === UPLOAD_ERR_FORM_SIZE) {
        return $error('La foto pesa más de lo que permite el servidor. Usa una más ligera.');
    }

    if ($codigo !== UPLOAD_ERR_OK) {
        return $error('La foto no se subió completa. Vuelve a intentarlo.');
    }

    if ($archivo['size'] > limite_foto()) {
        return $error('La foto pesa más de ' . limite_foto_legible() . '. Recórtala o bájale la calidad.');
    }

    // getimagesize mira el contenido: así no pasa un .exe renombrado a .jpg.
    $datos      = @getimagesize($archivo['tmp_name']);
    $permitidos = tipos_de_imagen();

    if (!$datos || !isset($permitidos[$datos[2]])) {
        return $error('Ese archivo no es una imagen. Sube un JPG, PNG, GIF o WEBP.');
    }

    $contenido = @file_get_contents($archivo['tmp_name']);

    if ($contenido === false || $contenido === '') {
        return $error('No se pudo leer la foto. Vuelve a intentarlo.');
    }

    return [
        'hay'       => true,
        'tipo'      => $permitidos[$datos[2]],
        'contenido' => $contenido,
        'error'     => '',
    ];
}
