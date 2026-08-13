<?php
/**
 * Lo que ve el cliente cuando el servidor no puede responder.
 * No consulta la base ni la configuración: tiene que poder dibujarse aunque
 * justamente eso sea lo que está fallando.
 */
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Volvemos en un momento</title>
<style>
  body {
    margin: 0;
    min-height: 100vh;
    display: grid;
    place-items: center;
    background: #faf6ef;
    color: #3d3229;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    padding: 1.5rem;
  }
  .aviso {
    max-width: 26rem;
    text-align: center;
    background: #fff;
    border: 1px solid #e7ddcd;
    border-radius: 1rem;
    box-shadow: 0 10px 30px rgba(61, 50, 41, .08);
    padding: 2.5rem 2rem;
  }
  .olla { font-size: 3.5rem; line-height: 1; }
  h1 { font-size: 1.35rem; margin: 1rem 0 .5rem; }
  p { margin: 0 0 1.25rem; line-height: 1.55; color: #6b5d4f; }
  a {
    display: inline-block;
    background: #b8532c;
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    padding: .7rem 1.6rem;
    border-radius: 999px;
  }
</style>
</head>
<body>
  <div class="aviso">
    <div class="olla">🍲</div>
    <h1>Estamos atendiendo la cocina</h1>
    <p>
      El menú no se pudo cargar en este momento.
      Vuelve a intentarlo en unos segundos.
    </p>
    <a href="">Reintentar</a>
  </div>
</body>
</html>
