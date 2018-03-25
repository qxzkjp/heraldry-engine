<?php
	require "requireAuth.php";
	if($_SESSION["accessLevel"] != 0 ){
		header('HTTP/1.0 403 Forbidden');
		exit("Fuck off, Chris.");
	}
?>