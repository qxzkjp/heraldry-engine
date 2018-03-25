<?php
	require "utility/connect.php";
	for( $i=0; $i<100; $i++){
		$stmt = $mysqli->prepare(
			"INSERT INTO failureLogs (userName, accessTime, IP, isIPv6) VALUES ('donkey', NOW(), INET6_ATON(?), IS_IPV6(?));");
		$stmt->bind_param("ss", $_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_ADDR']);
		$stmt->execute();
	}