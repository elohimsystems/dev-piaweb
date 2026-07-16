-- =====================================================
-- 1. Agregar columna controlparental a tmeventos
-- =====================================================
ALTER TABLE piaaccess.tmeventos ADD COLUMN controlparental boolean DEFAULT false;

-- =====================================================
-- 3. Crear tabla tmcontrolparental (si no existe)
-- =====================================================
CREATE SEQUENCE IF NOT EXISTS piaaccess.tmcontrolparental_id_seq INCREMENT BY 1 MINVALUE 1 START 1;

CREATE TABLE IF NOT EXISTS piaaccess.tmcontrolparental (
    id bigint NOT NULL DEFAULT nextval('piaaccess.tmcontrolparental_id_seq'::regclass),
    idevento bigint,
    edad_control integer,
    titulo_documento character varying(255),
    documento_pdf character varying(255),
    mensaje character varying(500),
    CONSTRAINT tmcontrolparental_pkey PRIMARY KEY (id)
);

-- =====================================================
-- 4. FK a tmeventos
-- =====================================================
CREATE INDEX IF NOT EXISTS idx_controlparental_idevento
    ON piaaccess.tmcontrolparental USING btree (idevento);

ALTER TABLE piaaccess.tmcontrolparental
    ADD CONSTRAINT fk_controlparental_evento
    FOREIGN KEY (idevento)
    REFERENCES piaaccess.tmeventos(id)
    ON DELETE CASCADE;
