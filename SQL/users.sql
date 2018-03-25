USE heraldry;

CREATE TABLE IF NOT EXISTS `Users` (
  ID int(11) NOT NULL AUTO_INCREMENT,
  userName varchar(50) NOT NULL,
  pHash varchar(255) NOT NULL,
  accessLevel int(11) NOT NULL,
  PRIMARY KEY (ID)
);

/*this creates a record for admin with password "password"*/
INSERT INTO USERS (userName, pHash, accessLevel)
VAULES ("admin", "$2y$10$0ZjS.jQJUWgiowJKwZFrpubNtliB0TEup7beIhiuNEL7l0WxNxMZK", 0);