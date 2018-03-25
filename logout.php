<?php
	require "utility/session.php";
	session_unset();
	session_destroy();
	header('Location: login.php', TRUE, 303);
	die();
?>