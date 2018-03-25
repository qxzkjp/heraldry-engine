USE heraldry;

CREATE TABLE IF NOT EXISTS `Users` (
  ID int(11) NOT NULL AUTO_INCREMENT,
  userName varchar(50) NOT NULL,
  pHash varchar(255) NOT NULL,
  accessLevel int(11) NOT NULL,
  PRIMARY KEY (ID)
);
