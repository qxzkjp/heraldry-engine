<?php
	require "utility/connect.php";
	for( $i=0; $i<100; $i++){
		$stmt = $mysqli->prepare(
			"INSERT INTO failureLogs (userName, accessTime, IP, isIPv6) VALUES ('donkey', NOW(), INET6_ATON(?), IS_IPV6(?));");
		$addr="FFFF:FFFF:FFFF:FFFF::1";
		$stmt->bind_param("ss", $addr, $addr);
		$stmt->execute();
	}