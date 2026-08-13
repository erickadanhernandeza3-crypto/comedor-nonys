-- Guarda las fotos de los platillos dentro de la base, no en el disco.
-- Hace falta porque en Render (y en cualquier servidor con disco efímero) los
-- archivos subidos se borran en cada reinicio o despliegue.
--
-- Ejecutar una sola vez sobre una base que ya existía:
--   mysql -u root comedor_nonys < sql/migracion_fotos.sql

ALTER TABLE platillos
  ADD COLUMN foto_tipo    VARCHAR(40) DEFAULT NULL AFTER imagen,
  ADD COLUMN foto_datos   MEDIUMBLOB  DEFAULT NULL AFTER foto_tipo,
  ADD COLUMN foto_version INT NOT NULL DEFAULT 0    AFTER foto_datos;
