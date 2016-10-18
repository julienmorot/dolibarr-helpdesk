create table llx_helpdesk_comment
(
  rowid     integer AUTO_INCREMENT PRIMARY KEY,
  datec     datetime NOT NULL,
  userid    integer NOT NULL,
  ticketid  integer NOT NULL,
  pubdesc   text,
  privdesc  text,
  contractid integer,
  timecard  decimal(5,2),
  techprofile integer
) ENGINE=innodb;

