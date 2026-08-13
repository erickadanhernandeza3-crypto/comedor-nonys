<?php
/**
 * Pasa a la base de datos las fotos que hoy están sueltas en assets/img.
 * Se corre una sola vez, desde la consola:
 *
 *   C:\xampp\php\php.exe herramientas\migrar_fotos.php
 *
 * Hace falta antes de publicar: en un servidor con disco efímero (Render) los
 * archivos del proyecto se pierden, pero lo que está en la base se queda.
 */
require_once __DIR__ . '/../includes/funciones.php';

if (PHP_SAPI !== 'cli') {
    exit('Esta herramienta se corre desde la consola.');
}

$platillos = consultar(
    'SELECT id, nombre, imagen FROM platillos
      WHERE imagen <> "" AND imagen IS NOT NULL AND foto_datos IS NULL'
);

if (!$platillos) {
    exit("No hay fotos sueltas que migrar.\n");
}

$permitidos = tipos_de_imagen();
$migradas   = 0;

foreach ($platillos as $platillo) {
    $ruta = $platillo['imagen'];

    // Las ligas de internet se quedan como están: no hay archivo que mover.
    if (strpos($ruta, 'http') === 0) {
        echo "   - {$platillo['nombre']}: es una liga, se deja igual\n";
        continue;
    }

    $archivo = RUTA_APP . '/' . $ruta;

    if (!is_file($archivo)) {
        echo "   ! {$platillo['nombre']}: no se encontró {$ruta}\n";
        continue;
    }

    $datos = getimagesize($archivo);

    if (!$datos || !isset($permitidos[$datos[2]])) {
        echo "   ! {$platillo['nombre']}: {$ruta} no es una imagen reconocible\n";
        continue;
    }

    $peso = filesize($archivo);

    if ($peso > limite_foto()) {
        echo "   ! {$platillo['nombre']}: pesa " . round($peso / 1024) . ' KB '
           . 'y el límite de este MySQL es ' . limite_foto_legible() . "\n";
        continue;
    }

    if (guardar_foto((int) $platillo['id'], $permitidos[$datos[2]], (string) file_get_contents($archivo))) {
        echo "   OK {$platillo['nombre']}: " . round($peso / 1024) . " KB guardados en la base\n";
        $migradas++;
    } else {
        echo "   ! {$platillo['nombre']}: MySQL rechazó la foto\n";
    }
}

echo "\nListo: {$migradas} de " . count($platillos) . " fotos ahora viven en la base.\n";
echo "Los archivos de assets/img ya no se usan; puedes conservarlos como respaldo.\n";
