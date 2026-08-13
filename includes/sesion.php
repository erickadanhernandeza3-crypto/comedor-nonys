<?php
/**
 * Sesión del panel y protección CSRF.
 */
require_once __DIR__ . '/funciones.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuario_actual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

/** Guarda en la sesión los datos mínimos del usuario que entró. */
function iniciar_sesion(array $usuario): void
{
    session_regenerate_id(true);

    $_SESSION['usuario'] = [
        'id'      => (int) $usuario['id'],
        'usuario' => $usuario['usuario'],
        'nombre'  => $usuario['nombre'] ?: $usuario['usuario'],
    ];
}

function cerrar_sesion(): void
{
    $_SESSION = [];
    session_destroy();
}

/** Corta la página y manda al login si no hay sesión. */
function requerir_sesion(): void
{
    if (!usuario_actual()) {
        header('Location: login.php');
        exit;
    }
}

function token_csrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

/** El token llega en el formulario o en la cabecera del fetch. */
function csrf_valido(array $entrada = []): bool
{
    $enviado = $entrada['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

    return $enviado !== '' && hash_equals($_SESSION['csrf'] ?? '', $enviado);
}
