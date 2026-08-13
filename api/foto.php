<?php
/**
 * Sirve la foto de un platillo guardada en la base. Pública, solo lectura.
 * GET api/foto.php?id=15&v=3   (v cambia al reemplazar la foto y rompe el caché)
 */
require_once __DIR__ . '/../includes/funciones.php';

$foto = foto_del_platillo(entero($_GET, 'id'));

if (!$foto) {
    http_response_code(404);
    exit;
}

header('Content-Type: ' . $foto['foto_tipo']);
header('Content-Length: ' . strlen($foto['foto_datos']));
header('Cache-Control: public, max-age=31536000, immutable');

echo $foto['foto_datos'];
