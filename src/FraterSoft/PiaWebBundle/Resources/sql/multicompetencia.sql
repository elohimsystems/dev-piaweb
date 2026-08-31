-- =============================================================================
-- Cambios de BD para la funcionalidad de inscripcion MULTICOMPETENCIA
-- (varias modalidades/competencias en un solo registro de inscrito).
--
-- Motor: PostgreSQL 9.5+  |  Esquema: piaaccess
-- Ejecutar ANTES de desplegar el codigo.
--
-- El panel SQL de algunos hosting no acepta BEGIN/COMMIT ni bloques DO $$,
-- por eso este script son sentencias simples. Ejecutar UNA sola vez.
-- Si el panel ejecuta una sentencia por vez, correr cada bloque por separado.
-- Si alguna linea falla con "already exists" / "column ... already exists",
-- esa parte ya estaba hecha: saltarla y seguir con el resto.
-- =============================================================================


-- -----------------------------------------------------------------------------
-- 1. tmeventos: flag para activar multicompetencia + titulo personalizable del
--    campo de modalidades en el formulario de inscripcion.
-- -----------------------------------------------------------------------------
ALTER TABLE piaaccess.tmeventos ADD COLUMN multicompetencia boolean DEFAULT false;

ALTER TABLE piaaccess.tmeventos ADD COLUMN titulocompetencias character varying(50);


-- -----------------------------------------------------------------------------
-- 2. tminscritos: en una inscripcion multicompetencia no hay una unica
--    competencia/categoria que representen el registro (el detalle real vive en
--    trinscritoscompetencias), asi que ambas columnas deben admitir NULL.
--    DROP NOT NULL no falla si la columna ya admite NULL.
-- -----------------------------------------------------------------------------
ALTER TABLE piaaccess.tminscritos ALTER COLUMN idcompetencia DROP NOT NULL;

ALTER TABLE piaaccess.tminscritos ALTER COLUMN idcategoria DROP NOT NULL;


-- -----------------------------------------------------------------------------
-- 3. trinscritoscompetencias: detalle de la inscripcion multicompetencia.
--    Una fila por cada modalidad en la que esta inscrito el competidor.
-- -----------------------------------------------------------------------------
CREATE SEQUENCE piaaccess.trinscritoscompetencias_id_seq INCREMENT BY 1 MINVALUE 1 START 1;

CREATE TABLE piaaccess.trinscritoscompetencias (
    id            bigint NOT NULL DEFAULT nextval('piaaccess.trinscritoscompetencias_id_seq'::regclass),
    idinscrito    bigint NOT NULL,
    idcompetencia bigint NOT NULL,
    idcategoria   bigint,
    precio        double precision,
    CONSTRAINT trinscritoscompetencias_pkey PRIMARY KEY (id)
);


-- -----------------------------------------------------------------------------
-- 4. Indices
-- -----------------------------------------------------------------------------
CREATE INDEX idx_trinscritoscompetencias_idinscrito    ON piaaccess.trinscritoscompetencias (idinscrito);

CREATE INDEX idx_trinscritoscompetencias_idcompetencia ON piaaccess.trinscritoscompetencias (idcompetencia);

CREATE INDEX idx_trinscritoscompetencias_idcategoria   ON piaaccess.trinscritoscompetencias (idcategoria);


-- -----------------------------------------------------------------------------
-- 5. Claves foraneas
-- -----------------------------------------------------------------------------
ALTER TABLE piaaccess.trinscritoscompetencias
    ADD CONSTRAINT fk_inscritoscompetencias_inscrito
    FOREIGN KEY (idinscrito) REFERENCES piaaccess.tminscritos(id) ON DELETE CASCADE;

ALTER TABLE piaaccess.trinscritoscompetencias
    ADD CONSTRAINT fk_inscritoscompetencias_competencia
    FOREIGN KEY (idcompetencia) REFERENCES piaaccess.tmcompetencias(id) ON DELETE RESTRICT;

ALTER TABLE piaaccess.trinscritoscompetencias
    ADD CONSTRAINT fk_inscritoscompetencias_categoria
    FOREIGN KEY (idcategoria) REFERENCES piaaccess.tmcategorias(id) ON DELETE RESTRICT;


-- =============================================================================
-- NOTA: el selector de moneda en los formularios de "precio por competencia" y
-- "precio por categoria" NO requiere cambios de BD: las columnas idmoneda ya
-- existen en tmprecioscompetencias y tmprecioscategorias; el formulario
-- simplemente pasa a usarlas. Conviene revisar los precios de competencia /
-- categoria ya cargados y asignarles la moneda correspondiente (idmoneda),
-- porque hasta ahora ese campo pudo quedar en NULL.
-- =============================================================================
