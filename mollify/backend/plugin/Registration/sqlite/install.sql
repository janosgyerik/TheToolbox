CREATE TABLE pending_registrations (
  id INTEGER PRIMARY KEY,
  name varchar(255) NOT NULL,
  password varchar(128) NOT NULL,
  email varchar(128) NOT NULL,
  key char(64) NOT NULL,
  time bigint(11) NOT NULL
);
INSERT INTO parameter (name, value) VALUES ('plugin_Registration_version', '1_0');