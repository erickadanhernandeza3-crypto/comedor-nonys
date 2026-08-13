<?php
/**
 * Diagnóstico de la conexión. Sirve para revisar, desde el navegador, qué datos
 * recibió el servidor y si la base responde. No enseña la contraseña.
 *
 * Bórralo cuando el sitio ya esté funcionando.
 */
require_once __DIR__ . '/config/db.php';

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store');

$cfg = config_bd();

/** Muestra el principio y el final de un texto, sin enseñarlo completo. */
function recortado(string $texto): string
{
    if ($texto === '') {
        return '(vacío)';
    }

    return strlen($texto) <= 12
        ? $texto
        : substr($texto, 0, 5) . '…' . substr($texto, -6);
}

echo "DIAGNÓSTICO DE COMEDOR NONY'S\n";
echo str_repeat('=', 46), "\n\n";

echo "PHP        : ", PHP_VERSION, "\n";
echo "mysqli     : ", extension_loaded('mysqli') ? 'sí' : 'NO', "\n\n";

echo "Host       : ", $cfg['host'], "\n";
echo "Puerto     : ", $cfg['puerto'], "\n";
echo "Base       : ", $cfg['nombre'], "\n";
echo "SSL        : ", $cfg['ssl'] ? 'sí' : 'no', "\n";
echo "Certificado: ", certificado_ca() ?: '(el del sistema)', "\n\n";

echo "Usuario    : ", recortado($cfg['usuario']), "\n";
echo "  largo    : ", strlen($cfg['usuario']), " caracteres\n";
echo "  termina  : ", str_contains($cfg['usuario'], '.')
    ? 'en "' . substr($cfg['usuario'], strrpos($cfg['usuario'], '.')) . '"  correcto para TiDB'
    : 'SIN PUNTO  <-- a TiDB le falta el sufijo, debe terminar en .root', "\n";
echo "  espacios : ", $cfg['usuario'] !== trim($cfg['usuario']) ? 'SÍ, sobran espacios' : 'no', "\n\n";

echo "Contraseña : ", $cfg['clave'] === '' ? '(VACÍA)' : strlen($cfg['clave']) . ' caracteres', "\n";
echo "  espacios : ", $cfg['clave'] !== trim($cfg['clave']) ? 'SÍ, sobran espacios' : 'no', "\n\n";

echo str_repeat('-', 46), "\n";

try {
    $filas = consultar('SELECT COUNT(*) AS n FROM platillos');
    echo "CONEXIÓN   : correcta\n";
    echo "Platillos  : ", $filas[0]['n'], "\n";
    echo "Fotos      : ", consultar('SELECT COUNT(*) AS n FROM platillos WHERE foto_datos IS NOT NULL')[0]['n'], "\n";
    echo "Hora MySQL : ", consultar('SELECT NOW() AS h')[0]['h'], "\n";
    echo "Hora PHP   : ", date('Y-m-d H:i:s'), "\n";
} catch (Throwable $e) {
    echo "CONEXIÓN   : FALLÓ\n";
    echo "Motivo     : ", $e->getMessage(), "\n";
}
