-- Objects : collapsed inheritance table with history
DROP TABLE IF EXISTS allObjects;
CREATE TABLE allObjects (
  -- OBJECT
  id       VARCHAR(128) NOT NULL,
  rev      INTEGER NOT NULL AUTO_INCREMENT,
  ts       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  type     VARCHAR(128)  NOT NULL,
  tags     VARCHAR(256),

  -- USER
  name     VARCHAR(128),
  pass     VARCHAR(128),
  email    VARCHAR(128),
  rights   VARCHAR(128),
  
  -- IDENTITY / SESSION
  user     VARCHAR(128), -- references a User's object

  -- CONTENT
  author   VARCHAR(128), -- references another Object's id
  children TEXT,         -- CSV-list of Object ids

  -- PAGECONTENT
  body     TEXT,
  
  -- NEWSCONTENT
  date     INTEGER,

  PRIMARY KEY (id, rev)
) ENGINE=MyISAM;

CREATE OR REPLACE VIEW current AS
  SELECT id AS cid, MAX(rev) AS revision, MIN(ts) AS created, MAX(ts) AS updated 
    FROM allObjects
GROUP BY id;

CREATE OR REPLACE VIEW objects AS
  SELECT * FROM allObjects 
  JOIN current ON id = cid AND rev = revision;
