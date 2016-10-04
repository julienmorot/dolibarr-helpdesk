create table llx_helpdesk_ticket
(
  rowid     integer AUTO_INCREMENT PRIMARY KEY,
  datec     datetime NOT NULL,
  datem     datetime NULL,
  dater     datetime NULL,
  entity    integer DEFAULT 1 NOT NULL,
  createdby integer,
  closedby  integer,
  state     varchar(20),
  title     varchar(255) NOT NULL,
  description text
) ENGINE=innodb;


