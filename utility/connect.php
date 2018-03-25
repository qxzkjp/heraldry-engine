<?php
$config = require __DIR__ . "/../bootstrap/bootstrap.php";

try {
	$mysqli = new mysqli(
		$config['db.host'],
		$config['db.user'],
		$config['db.pass'],
		$config['db.name']
	);
	$mysqli->set_charset("utf8mb4");
} catch(Exception $e) {
	error_log($e->getMessage());
	exit('Error connecting to database'); //Should be a message a typical user could understand
}