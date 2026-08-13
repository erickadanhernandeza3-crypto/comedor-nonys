<?php
/**
 * Modelo: platillos del menú general.
 *
 * La foto vive en la propia base (foto_tipo + foto_datos). Como ese binario
 * puede pesar megas, nunca se pide con SELECT *: los listados usan la lista de
 * columnas de columnas_platillo() y el binario solo se lee al servir la imagen.
 */
require_once __DIR__ . '/../config/db.php';

/** Tamaño de los pedazos con los que se manda la foto a MySQL. */
const PEDAZO_FOTO = 524288;

/** Columnas del platillo sin el binario de la foto. */
function columnas_platillo(string $alias = ''): string
{
    $p = $alias !== '' ? $alias . '.' : '';

    return "{$p}id, {$p}categoria_id, {$p}nombre, {$p}descripcion, {$p}precio, {$p}imagen,
            {$p}foto_tipo, {$p}foto_version, {$p}disponible, {$p}destacado, {$p}orden, {$p}activo";
}

/** Listado del panel: platillos activos con el nombre de su categoría. */
function platillos_con_categoria(): array
{
    return consultar(
        'SELECT ' . columnas_platillo('p') . ', c.nombre AS categoria, c.icono
           FROM platillos p
           JOIN categorias c ON c.id = p.categoria_id
          WHERE p.activo = 1
          ORDER BY c.orden, p.orden, p.nombre'
    );
}

/** Versión corta para el selector de "tomar del menú general". */
function platillos_para_elegir(): array
{
    return consultar(
        'SELECT p.id, p.nombre, p.descripcion, p.precio, c.nombre AS categoria
           FROM platillos p
           JOIN categorias c ON c.id = p.categoria_id
          WHERE p.activo = 1
          ORDER BY c.orden, p.nombre'
    );
}

function platillo(int $id): ?array
{
    return consultar_una(
        'SELECT ' . columnas_platillo() . ' FROM platillos WHERE id = ?',
        [$id],
        'i'
    );
}

/** El binario de la foto. Solo lo usa api/foto.php al servirla. */
function foto_del_platillo(int $id): ?array
{
    $fila = consultar_una(
        'SELECT foto_tipo, foto_datos FROM platillos WHERE id = ? AND foto_datos IS NOT NULL',
        [$id],
        'i'
    );

    return $fila ?: null;
}

function crear_platillo(array $datos): int
{
    return insertar(
        'INSERT INTO platillos (categoria_id, nombre, descripcion, precio, imagen, disponible, destacado)
         VALUES (?, ?, ?, ?, ?, ?, ?)',
        [
            $datos['categoria_id'],
            $datos['nombre'],
            $datos['descripcion'],
            $datos['precio'],
            $datos['imagen'],
            $datos['disponible'],
            $datos['destacado'],
        ],
        'issdsii'
    );
}

function actualizar_platillo(int $id, array $datos): int
{
    return ejecutar(
        'UPDATE platillos
            SET categoria_id = ?, nombre = ?, descripcion = ?, precio = ?,
                imagen = ?, disponible = ?, destacado = ?
          WHERE id = ?',
        [
            $datos['categoria_id'],
            $datos['nombre'],
            $datos['descripcion'],
            $datos['precio'],
            $datos['imagen'],
            $datos['disponible'],
            $datos['destacado'],
            $id,
        ],
        'issdsiii'
    );
}

/**
 * Guarda la foto dentro de la base. Se manda por pedazos con send_long_data
 * para no toparse con el límite de max_allowed_packet del servidor.
 */
function guardar_foto(int $id, string $tipo, string $contenido): bool
{
    $stmt = db()->prepare(
        'UPDATE platillos
            SET foto_tipo = ?, foto_datos = ?, foto_version = foto_version + 1, imagen = ""
          WHERE id = ?'
    );

    $nulo = null;
    $stmt->bind_param('sbi', $tipo, $nulo, $id);

    foreach (str_split($contenido, PEDAZO_FOTO) as $pedazo) {
        $stmt->send_long_data(1, $pedazo);
    }

    try {
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        // Sobre todo cuando la foto excede max_allowed_packet del servidor.
        return false;
    } finally {
        $stmt->close();
    }

    return true;
}

function quitar_foto(int $id): int
{
    return ejecutar(
        'UPDATE platillos
            SET foto_tipo = NULL, foto_datos = NULL, foto_version = foto_version + 1, imagen = ""
          WHERE id = ?',
        [$id],
        'i'
    );
}

function eliminar_platillo(int $id): int
{
    return ejecutar('DELETE FROM platillos WHERE id = ?', [$id], 'i');
}

function cambiar_disponible_platillo(int $id, bool $disponible): int
{
    return ejecutar(
        'UPDATE platillos SET disponible = ? WHERE id = ?',
        [$disponible ? 1 : 0, $id],
        'ii'
    );
}
