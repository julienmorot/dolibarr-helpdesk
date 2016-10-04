create table llx_helpdesk_comment
(
  rowid     integer AUTO_INCREMENT PRIMARY KEY,
  datec     datetime NOT NULL,
  userid    integer,
  pubdesc   text,
  privdesc  text
) ENGINE=innodb;

