-- =====================================================
-- 1. Agregar columna multicompetencia a tmeventos
-- =====================================================
ALTER TABLE piaaccess.tmeventos ADD COLUMN multicompetencia boolean DEFAULT false;
ALTER TABLE piaaccess.tmeventos ADD COLUMN titulocompetencias character varying(50);

-- =====================================================
-- 1.1 Permitir idcompetencia nulo en tminscritos: en una inscripcion
-- multicompetencia no hay una unica competencia que representarla alli,
-- el detalle real queda en trinscritoscompetencias
-- =====================================================
ALTER TABLE piaaccess.tminscritos ALTER COLUMN idcompetencia DROP NOT NULL;

-- =====================================================
-- 2. Crear tabla trinscritoscompetencias (si no existe)
-- =====================================================
CREATE SEQUENCE IF NOT EXISTS piaaccess.trinscritoscompetencias_id_seq INCREMENT BY 1 MINVALUE 1 START 1;

CREATE TABLE IF NOT EXISTS piaaccess.trinscritoscompetencias (
    id bigint NOT NULL DEFAULT nextval('piaaccess.trinscritoscompetencias_id_seq'::regclass),
    idinscrito bigint NOT NULL,
    idcompetencia bigint NOT NULL,
    idcategoria bigint,
    precio double precision,
    CONSTRAINT trinscritoscompetencias_pkey PRIMARY KEY (id)
);

-- =====================================================
-- 3. Indices y FKs
-- =====================================================
CREATE INDEX IF NOT EXISTS idx_trinscritoscompetencias_idinscrito
    ON piaaccess.trinscritoscompetencias USING btree (idinscrito);

CREATE INDEX IF NOT EXISTS idx_trinscritoscompetencias_idcompetencia
    ON piaaccess.trinscritoscompetencias USING btree (idcompetencia);

CREATE INDEX IF NOT EXISTS idx_trinscritoscompetencias_idcategoria
    ON piaaccess.trinscritoscompetencias USING btree (idcategoria);

ALTER TABLE piaaccess.trinscritoscompetencias
    ADD CONSTRAINT fk_inscritoscompetencias_inscrito
    FOREIGN KEY (idinscrito)
    REFERENCES piaaccess.tminscritos(id)
    ON DELETE CASCADE;

ALTER TABLE piaaccess.trinscritoscompetencias
    ADD CONSTRAINT fk_inscritoscompetencias_competencia
    FOREIGN KEY (idcompetencia)
    REFERENCES piaaccess.tmcompetencias(id)
    ON DELETE RESTRICT;

ALTER TABLE piaaccess.trinscritoscompetencias
    ADD CONSTRAINT fk_inscritoscompetencias_categoria
    FOREIGN KEY (idcategoria)
    REFERENCES piaaccess.tmcategorias(id)
    ON DELETE RESTRICT;
