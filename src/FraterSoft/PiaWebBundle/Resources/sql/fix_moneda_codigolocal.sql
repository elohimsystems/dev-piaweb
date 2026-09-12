-- =============================================================================
-- Corrige el campo tmmonedas.codigolocal, que estaba mapeado como CHAR(5)
-- (character fijo) en vez de VARCHAR(5). PostgreSQL rellena con espacios en
-- blanco al final los CHAR fijos hasta completar el largo (ej. "Ksh" se
-- guarda como "Ksh  "), lo que dejaba invisible el texto de la moneda en los
-- radio buttons del formulario de inscripcion (el texto SI estaba, pero
-- quedaba corrido/oculto por los espacios).
--
-- Motor: PostgreSQL 9.5+  |  Esquema: piaaccess
-- Ejecutar ANTES de desplegar el codigo (Moneda.orm.yml ya no declara
-- "fixed: true" para este campo).
--
-- El panel SQL de algunos hosting no acepta BEGIN/COMMIT ni bloques DO $$,
-- por eso este script son sentencias simples. Ejecutar UNA sola vez.
-- =============================================================================


-- -----------------------------------------------------------------------------
-- 1. Convierte la columna de character(5) fijo a varchar(5), recortando de
--    una vez los espacios en blanco que ya tenian los valores guardados.
-- -----------------------------------------------------------------------------
ALTER TABLE piaaccess.tmmonedas
    ALTER COLUMN codigolocal TYPE varchar(5)
    USING trim(trailing from codigolocal);


-- -----------------------------------------------------------------------------
-- 2. Verificacion: confirma que ya no queda ningun codigolocal con espacios
--    en blanco al final (la columna "len" debe ser igual a la longitud del
--    texto sin espacios).
-- -----------------------------------------------------------------------------
SELECT id, nombre, codigolocal, length(codigolocal) AS len
FROM piaaccess.tmmonedas
ORDER BY id;
