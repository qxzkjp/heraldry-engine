USE heraldry;

CREATE TABLE failureLogs (
	logNum int NOT NULL AUTO_INCREMENT,
	userID int NOT NULL,
	accessTime datetime NOT NULL,
	IP VARBINARY(16) NOT NULL,
	isIPv6 BOOLEAN NOT NULL,
	PRIMARY KEY(logNum)
)

INSERT INTO failureLogs (userID, accessTime, IP, isIPv6) VALUES (?, NOW(), INET6_ATON(?), IS_IPV6(?));

SELECT * FROM failureLogs WHERE userID=? AND accessTime > (NOW() - INTERVAL 5 MINUTE)