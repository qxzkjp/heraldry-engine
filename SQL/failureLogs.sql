USE heraldry;

CREATE TABLE failureLogs (
	logNum int NOT NULL AUTO_INCREMENT,
	userName VARCHAR(50) NOT NULL,
	accessTime datetime NOT NULL,
	IP VARBINARY(16) NOT NULL,
	isIPv6 BOOLEAN NOT NULL,
	PRIMARY KEY(logNum)
)

INSERT INTO failureLogs (userID, accessTime, IP, isIPv6) VALUES (?, NOW(), INET6_ATON(?), IS_IPV6(?));

SELECT COUNT(*) FROM failureLogs WHERE userName=? AND accessTime > (NOW() - INTERVAL 5 MINUTE);

/*access counts from individual subnets*/
SELECT userName, SUBSTR(IP,1,8) as subnet, COUNT(*) FROM failureLogs WHERE isIPv6 = TRUE GROUP BY userName, subnet