<?php
	require "requireAuth.php";
	if($_SESSION["accessLevel"] != 0 ){
		exit("Fuck off, Chris.");
	}
?>