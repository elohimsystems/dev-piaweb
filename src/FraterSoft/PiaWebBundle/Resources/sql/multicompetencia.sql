-- =============================================================================
-- Cambios de BD para la funcionalidad de inscripcion MULTICOMPETENCIA
-- (varias modalidades/competencias en un solo registro de inscrito).
--
-- Motor: PostgreSQL 9.5  |  Esquema: piaaccess
-- Script idempotente: se puede ejecutar varias veces sin error.
-- Ejecutar ANTES de desplegar el codigo.
--
-- Uso:  psql -h <host> -U <usuario> -d <basededatos> -f multicompetencia.sql
-- =============================================================================

BEGIN;

-- -----------------------------------------------------------------------------
-- 1. tmeventos: flag para activar multicompetencia + titulo personalizable del
--    campo de modalidades en el formulario de inscripcion.
-- -----------------------------------------------------------------------------
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = 'piaaccess' AND table_name = 'tmeventos'
          AND column_name = 'multicompetencia'
    ) THEN
        ALTER TABLE piaaccess.tmeventos
            ADD COLUMN multicompetencia boolean DEFAULT false;
        COMMENT ON COLUMN piaaccess.tmeventos.multicompetencia IS
            'Permite inscribirse en varias competencias/modalidades a la vez';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = 'piaaccess' AND table_name = 'tmeventos'
          AND column_name = 'titulocompetencias'
    ) THEN
        ALTER TABLE piaaccess.tmeventos
            ADD COLUMN titulocompetencias character varying(50);
        COMMENT ON COLUMN piaaccess.tmeventos.titulocompetencias IS
            'Titulo del campo de seleccion de competencias en el formulario (por defecto "Modalidades")';
    END IF;
END
$$;

-- -----------------------------------------------------------------------------
-- 2. tminscritos: en una inscripcion multicompetencia no hay una unica
--    competencia/categoria que representen el registro (el detalle real vive en
--    trinscritoscompetencias), asi que ambas columnas deben admitir NULL.
--    ALTER ... DROP NOT NULL no falla si la columna ya admite NULL.
-- -----------------------------------------------------------------------------
ALTER TABLE piaaccess.tminscritos ALTER COLUMN idcompetencia DROP NOT NULL;
ALTER TABLE piaaccess.tminscritos ALTER COLUMN idcategoria   DROP NOT NULL;

-- -----------------------------------------------------------------------------
-- 3. trinscritoscompetencias: detalle de la inscripcion multicompetencia.
--    Una fila por cada modalidad en la que esta inscrito el competidor.
-- -----------------------------------------------------------------------------
CREATE SEQUENCE IF NOT EXISTS piaaccess.trinscritoscompetencias_id_seq
    INCREMENT BY 1 MINVALUE 1 START 1;

CREATE TABLE IF NOT EXISTS piaaccess.trinscritoscompetencias (
    id            bigint NOT NULL DEFAULT nextval('piaaccess.trinscritoscompetencias_id_seq'::regclass),
    idinscrito    bigint NOT NULL,
    idcompetencia bigint NOT NULL,
    idcategoria   bigint,
    precio        double precision,
    CONSTRAINT trinscritoscompetencias_pkey PRIMARY KEY (id)
);

-- Indices
CREATE INDEX IF NOT EXISTS idx_trinscritoscompetencias_idinscrito
    ON piaaccess.trinscritoscompetencias USING btree (idinscrito);
CREATE INDEX IF NOT EXISTS idx_trinscritoscompetencias_idcompetencia
    ON piaaccess.trinscritoscompetencias USING btree (idcompetencia);
CREATE INDEX IF NOT EXISTS idx_trinscritoscompetencias_idcategoria
    ON piaaccess.trinscritoscompetencias USING btree (idcategoria);

-- Claves foraneas (PostgreSQL 9.5 no soporta ADD CONSTRAINT IF NOT EXISTS)
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'fk_inscritoscompetencias_inscrito') THEN
        ALTER TABLE piaaccess.trinscritoscompetencias
            ADD CONSTRAINT fk_inscritoscompetencias_inscrito
            FOREIGN KEY (idinscrito) REFERENCES piaaccess.tminscritos(id)
            ON DELETE CASCADE;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'fk_inscritoscompetencias_competencia') THEN
        ALTER TABLE piaaccess.trinscritoscompetencias
            ADD CONSTRAINT fk_inscritoscompetencias_competencia
            FOREIGN KEY (idcompetencia) REFERENCES piaaccess.tmcompetencias(id)
            ON DELETE RESTRICT;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'fk_inscritoscompetencias_categoria') THEN
        ALTER TABLE piaaccess.trinscritoscompetencias
            ADD CONSTRAINT fk_inscritoscompetencias_categoria
            FOREIGN KEY (idcategoria) REFERENCES piaaccess.tmcategorias(id)
            ON DELETE RESTRICT;
    END IF;
END
$$;

COMMIT;

-- =============================================================================
-- NOTA: el selector de moneda en los formularios de "precio por competencia" y
-- "precio por categoria" NO requiere cambios de BD: las columnas idmoneda ya
-- existen en tmprecioscompetencias y tmprecioscategorias; el formulario
-- simplemente pasa a usarlas. Conviene revisar los precios de competencia /
-- categoria ya cargados y asignarles la moneda correspondiente (idmoneda),
-- porque hasta ahora ese campo pudo quedar en NULL.
-- =============================================================================
