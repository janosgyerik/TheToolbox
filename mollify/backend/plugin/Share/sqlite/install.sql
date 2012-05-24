CREATE TABLE share (
  id char(32) PRIMARY KEY,
  item_id char(255) NOT NULL,
  name varchar(255) NOT NULL,
  user_id INTEGER NOT NULL,
  expiration bigint(11) NOT NULL,
  created bigint(11) NOT NULL,
  active TINYINT(1) NOT NULL
);
INSERT INTO parameter (name, value) VALUES ('plugin_Share_version', '1_1');