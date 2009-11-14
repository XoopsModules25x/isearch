CREATE TABLE isearch_searches (
  isearchid int(10) unsigned NOT NULL auto_increment,
  keyword varchar(100) NOT NULL default '',
  datesearch datetime NOT NULL default '0000-00-00 00:00:00',
  uid mediumint(8) unsigned NOT NULL default '0',
  ip varchar(32) NOT NULL default '',
  PRIMARY KEY  (isearchid),
  KEY keyword (keyword,uid),
  KEY uid (uid),
  KEY datesearch (datesearch),
  FULLTEXT KEY keyword_2 (keyword)
) TYPE=MyISAM;

