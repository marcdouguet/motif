PRAGMA encoding = 'UTF-8';
PRAGMA page_size = 8192;  -- blob optimisation https://www.sqlite.org/intern-v-extern-blob.html
PRAGMA foreign_keys = ON;
-- The VACUUM command may change the ROWIDs of entries in any tables that do not have an explicit INTEGER PRIMARY KEY

DROP TABLE IF EXISTS patterns;
DROP TABLE IF EXISTS lines;
DROP TABLE IF EXISTS plays;
DROP TABLE IF EXISTS verse;
DROP TABLE IF EXISTS prose;

CREATE TABLE patterns (
  -- une pièce
  id         INTEGER, -- rowid auto
  play       TEXT,    -- play id
  author     TEXT,    -- auteur lisible
  title      TEXT,    -- titre lisible
  genre      TEXT,    -- nom de genre
  created    INTEGER, -- année de création
  line_n       TEXT,    -- 
  act_n		 INTEGER,
  scene_n		 INTEGER,
  line_id	 TEXT,
  line       TEXT,
  position	 INTEGER,
  graph      TEXT,    -- 
  cat		 TEXT,
  graph_full TEXT,
  cat_full	 TEXT,	
  graph_ref  TEXT,

  PRIMARY KEY(id ASC)
);
CREATE UNIQUE INDEX patterns_id ON patterns(id);
CREATE INDEX patterns_author ON patterns(author);
CREATE INDEX patterns_play ON patterns(play);
CREATE INDEX patterns_graph ON patterns(graph);
CREATE INDEX patterns_cat ON patterns(cat);

CREATE TABLE lines (
  -- une pièce
  id         INTEGER, -- rowid auto
  play       TEXT,    -- play id
  author     TEXT,    -- auteur lisible
  title      TEXT,    -- titre lisible
  genre      TEXT,    -- nom de genre
  created    INTEGER, -- année de création
  line_n       TEXT,    -- 
  act_n		 INTEGER,
  scene_n		 INTEGER,
  line_id	 TEXT,
  graph      TEXT,    -- 
  cat		 TEXT,
  graph_full TEXT,
  cat_full	 TEXT,	
  graph_ref  TEXT,

  PRIMARY KEY(id ASC)
);
CREATE UNIQUE INDEX lines_id ON lines(id);
CREATE INDEX lines_author ON lines(author);
CREATE INDEX lines_play ON lines(play);
CREATE INDEX lines_graph ON lines(graph);
CREATE INDEX lines_cat ON lines(cat);


CREATE TABLE plays (
	id			INTEGER,
	play		INTEGER,
	l			INTEGER,
	author      TEXT,    -- auteur lisible
	title       TEXT,    -- titre lisible
	genre       TEXT,    -- nom de genre
	created     INTEGER, -- année de création
	prose		TEXT,    -- verse/prose
	
	PRIMARY KEY(id ASC)
	
);

CREATE TABLE verse (
  id         INTEGER, -- rowid auto
  play       TEXT,    -- play id
  graph      TEXT,    -- 
  cat		 TEXT,
  graph_full TEXT,
  cat_full	 TEXT,	
  l			 INTEGER,		
  PRIMARY KEY(id ASC)
	
);


CREATE TABLE prose (
  id         INTEGER, -- rowid auto
  play       TEXT,    -- play id
  graph      TEXT,    -- 
  cat		 TEXT,
  graph_full TEXT,
  cat_full	 TEXT,	
  l			 INTEGER,		
  PRIMARY KEY(id ASC)
	
);