<?php
/**
 * Conexión a MySQL y funciones básicas de consulta.
 * Es la única capa que habla con mysqli: los modelos usan estas funciones.
 *
 * En tu XAMPP no hay nada que configurar: los valores por defecto ya son los de
 * siempre. En un servidor (Render y compañía) los datos llegan por variables de
 * entorno, así que las contraseñas nunca viven dentro del código.
 */

date_default_timezone_set(getenv('TZ_PHP') ?: 'America/Mexico_City');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/** Datos de conexión: del entorno si existen, si no los de XAMPP. */
function config_bd(): array
{
    return [
        'host'    => getenv('DB_HOST')    ?: '127.0.0.1',
        'usuario' => getenv('DB_USUARIO') ?: 'root',
        'clave'   => getenv('DB_CLAVE')   ?: '',
        'nombre'  => getenv('DB_NOMBRE')  ?: 'comedor_nonys',
        'puerto'  => (int) (getenv('DB_PUERTO') ?: 3306),
        // Los MySQL en la nube (TiDB, Aiven, PlanetScale) exigen conexión cifrada.
        'ssl'     => getenv('DB_SSL') === '1',
        'zona'    => getenv('TZ_MYSQL') ?: '-06:00',
    ];
}

function db(): mysqli
{
    static $conexion = null;

    if ($conexion !== null) {
        return $conexion;
    }

    $cfg      = config_bd();
    $conexion = mysqli_init();

    if ($cfg['ssl']) {
        $conexion->ssl_set(null, null, certificado_ca(), null, null);
    }

    $conexion->real_connect(
        $cfg['host'],
        $cfg['usuario'],
        $cfg['clave'],
        $cfg['nombre'],
        $cfg['puerto'],
        null,
        $cfg['ssl'] ? MYSQLI_CLIENT_SSL : 0
    );

    $conexion->set_charset('utf8mb4');

    // PHP y MySQL deben coincidir en la fecha: si no, "la comida de hoy" se
    // guarda en un día y se consulta en otro durante las horas de diferencia.
    $conexion->query("SET time_zone = '" . $conexion->real_escape_string($cfg['zona']) . "'");

    return $conexion;
}

/** Certificado raíz para validar al servidor. Null deja que lo busque el sistema. */
function certificado_ca(): ?string
{
    $ruta = getenv('DB_CA') ?: '/etc/ssl/certs/ca-certificates.crt';

    return is_file($ruta) ? $ruta : null;
}

/** Prepara la consulta, amarra los parámetros y la ejecuta. */
function preparar(string $sql, array $params, string $tipos): mysqli_stmt
{
    $stmt = db()->prepare($sql);

    if ($params) {
        $stmt->bind_param($tipos ?: str_repeat('s', count($params)), ...$params);
    }

    $stmt->execute();

    return $stmt;
}

/** Devuelve todas las filas de un SELECT. */
function consultar(string $sql, array $params = [], string $tipos = ''): array
{
    $stmt  = preparar($sql, $params, $tipos);
    $filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $filas;
}

/** Devuelve la primera fila de un SELECT, o null si no hubo resultados. */
function consultar_una(string $sql, array $params = [], string $tipos = ''): ?array
{
    return consultar($sql, $params, $tipos)[0] ?? null;
}

/** INSERT / UPDATE / DELETE. Devuelve cuántas filas cambiaron. */
function ejecutar(string $sql, array $params = [], string $tipos = ''): int
{
    $stmt  = preparar($sql, $params, $tipos);
    $filas = $stmt->affected_rows;
    $stmt->close();

    return max($filas, 0);
}

/** INSERT que devuelve el id recién generado. */
function insertar(string $sql, array $params = [], string $tipos = ''): int
{
    $stmt = preparar($sql, $params, $tipos);
    $id   = (int) db()->insert_id;
    $stmt->close();

    return $id;
}
