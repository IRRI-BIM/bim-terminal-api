--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: dataset; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE dataset (
    id integer NOT NULL,
    transaction_id integer,
    input_file json,
    creator character varying(150),
    creation_timestamp timestamp with time zone,
    modifier character varying(150),
    modification_timestamp timestamp with time zone,
    is_void boolean,
    remarks text,
    committed_records_count integer,
    status character varying(30)
);


ALTER TABLE public.dataset OWNER TO postgres;

--
-- Name: dataset_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE dataset_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dataset_id_seq OWNER TO postgres;

--
-- Name: dataset_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE dataset_id_seq OWNED BY dataset.id;


--
-- Name: record; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE record (
    id integer NOT NULL,
    variable character varying(150),
    value character varying(255),
    data_level character varying(30),
    is_data_type_valid boolean,
    is_data_value_valid boolean,
    creator character varying(150),
    creation_timestamp timestamp with time zone,
    modifier character varying(150),
    modification_timestamp timestamp with time zone,
    is_void boolean,
    remarks text,
    is_committed boolean,
    transaction_id integer
);


ALTER TABLE public.record OWNER TO postgres;

--
-- Name: record_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE record_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.record_id_seq OWNER TO postgres;

--
-- Name: record_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE record_id_seq OWNED BY record.id;


--
-- Name: transaction; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE transaction (
    id integer NOT NULL,
    status character varying(30),
    start_action_timestamp timestamp with time zone,
    end_action_timestamp timestamp with time zone,
    creator character varying(150),
    modifier character varying(150),
    modification_timestamp timestamp with time zone,
    is_void boolean DEFAULT false,
    remarks text,
    record_count integer,
    invalid_record_count integer,
    study_name character varying(150)
);


ALTER TABLE public.transaction OWNER TO postgres;

--
-- Name: transaction_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE transaction_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.transaction_id_seq OWNER TO postgres;

--
-- Name: transaction_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE transaction_id_seq OWNED BY transaction.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY dataset ALTER COLUMN id SET DEFAULT nextval('dataset_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY record ALTER COLUMN id SET DEFAULT nextval('record_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY transaction ALTER COLUMN id SET DEFAULT nextval('transaction_id_seq'::regclass);


--
-- Data for Name: dataset; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY dataset (id, transaction_id, input_file, creator, creation_timestamp, modifier, modification_timestamp, is_void, remarks, committed_records_count, status) FROM stdin;
\.


--
-- Name: dataset_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('dataset_id_seq', 1, false);


--
-- Data for Name: record; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY record (id, variable, value, data_level, is_data_type_valid, is_data_value_valid, creator, creation_timestamp, modifier, modification_timestamp, is_void, remarks, is_committed, transaction_id) FROM stdin;
\.


--
-- Name: record_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('record_id_seq', 1, false);


--
-- Data for Name: transaction; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY transaction (id, status, start_action_timestamp, end_action_timestamp, creator, modifier, modification_timestamp, is_void, remarks, record_count, invalid_record_count, study_name) FROM stdin;
\.


--
-- Name: transaction_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('transaction_id_seq', 14, true);


--
-- Name: dataset_id_pk; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY dataset
    ADD CONSTRAINT dataset_id_pk PRIMARY KEY (id);


--
-- Name: record_id_pk; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY record
    ADD CONSTRAINT record_id_pk PRIMARY KEY (id);


--
-- Name: transaction_id_pk; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY transaction
    ADD CONSTRAINT transaction_id_pk PRIMARY KEY (id);


--
-- Name: dataset_transaction_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY dataset
    ADD CONSTRAINT dataset_transaction_id_fk FOREIGN KEY (transaction_id) REFERENCES transaction(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: record_transaction_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY record
    ADD CONSTRAINT record_transaction_id_fk FOREIGN KEY (transaction_id) REFERENCES transaction(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

