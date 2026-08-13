<?php
/**
 * Modelo: usuarios del panel.
 */
require_once __DIR__ . '/../config/db.php';

function usuario_por_nombre(string $usuario): ?array
{
    return consultar_una('SELECT * FROM usuarios WHERE usuario = ? LIMIT 1', [$usuario]);
}

function usuario_por_id(int $id): ?array
{
    return consultar_una('SELECT * FROM usuarios WHERE id = ?', [$id], 'i');
}

/** Comprueba una contraseña en claro contra el hash guardado. */
function contrasena_correcta(int $id, string $clave): bool
{
    $usuario = usuario_por_id($id);

    return $usuario !== null && password_verify($clave, $usuario['contrasena']);
}

function guardar_contrasena(int $id, string $clave): int
{
    return ejecutar(
        'UPDATE usuarios SET contrasena = ? WHERE id = ?',
        [password_hash($clave, PASSWORD_DEFAULT), $id],
        'si'
    );
}
