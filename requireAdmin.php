<?php
	if(!array_key_exists('accessLevel',$_SESSION)
		|| $_SESSION["accessLevel"] == 0 ){
		header('Location: login.php', TRUE, 303);
		exit("tried to redirect");
	}
?>