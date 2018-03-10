<?php
	$host='localhost';
	$dbUser='root';
	$dbPass='';
	$dbName='heraldry';
	$lifetime=600;
	session_set_cookie_params ( $lifetime, '/' , '.heraldryengine.com' , TRUE );
	session_start();
	setcookie(session_name(),session_id(),time()+$lifetime);
	$uname="";
	$pword="";
	if(array_key_exists('username',$_POST)){
		$uname=$_POST['username'];
		if(array_key_exists('password',$_POST)){
			$ph=password_hash($_POST['password'], PASSWORD_DEFAULT);
			$pword=$_POST['password'];
		}
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		try {
		  $mysqli = new mysqli($host, $dbUser, $dbPass, $dbName);
		  $mysqli->set_charset("utf8mb4");
		} catch(Exception $e) {
		  error_log($e->getMessage());
		  exit('Error connecting to database'); //Should be a message a typical user could understand
		}
		$stmt = $mysqli->prepare("SELECT * FROM users WHERE userName = ?");
		$stmt->bind_param("s", $uname);
		$stmt->execute();
		$result = $stmt->get_result();
		//if($result->num_rows === 0) exit('No rows');
		$ids=[];
		while($row = $result->fetch_assoc()) {
			$ids[] = array(
				'ID'=>$row['ID'],
				'pHash' => $row['pHash']);
		}
		$stmt->close();
		foreach($ids as $value){
			if(password_verify($pword,$value['pHash'])){
				$_SESSION['userID']=(int)$value['ID'];
				header('Location: index.php', TRUE, 303);
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
		<title>Heraldry Engine</title>
		<link rel="icon" type="image/x-icon" href="logo.png" />
		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<link href="styles/style.css" rel="stylesheet" type="text/css">
		<link id="heraldry-css" href="styles/heraldry.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="path-data-polyfill.js/path-data-polyfill.js"></script>
		<script type="text/javascript" src="scripts/jquery-3.2.1.min.js"></script>
		<!--script type="text/javascript" src="../base64-js/base64js.min.js"></!--script-->
		<script type="text/javascript" src="../TextEncoderLite/index.js"></script>
		<script type="text/javascript" src="scripts/cubic.js"></script>
		<script type="text/javascript" src="scripts/syntax.js"></script>
        <script type="text/javascript" src="scripts/svg.js"></script>
		<script type="text/javascript" src="scripts/ui.js"></script>
	</head>
	<body>
		<div id="menuContainer">
            <div id="innerMenuContainer">
                <svg version="1.1" baseProfile="full" viewBox="0 0 100 100" id="menuButton" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0" y="10" width="100" height="20" rx="10" ry="10" />
                    <rect x="0" y="40" width="100" height="20" rx="10" ry="10" />
                    <rect x="0" y="70" width="100" height="20" rx="10" ry="10" />
                </svg>
                <div id="sideMenu" style="display:none">
                    <div id="topMenuSpacer" class="clear"></div>
                    <div id="innerMenu">
                        <div class="menu-list">
                            <a href="#" class="menu-item">What is this?</a>
                            <p class="menu-item" id="exampleBlazons">Example blazons</p>
                            <div style="display:none" id="exampleContainer">
                                <div class="demoBlazon"><i>Scrope v Grosvenor</i> (arms of Baron Scrope)<span class="blazonText">Azure, a bend Or</span></div>
                                <div class="demoBlazon">Arms of the town of Gerville, France<span class="blazonText">Argent, on a bend Azure between two phrygian caps Gules three mullets of six points Or</span></div>
                                <div class="demoBlazon">Old arms of France<span class="blazonText">Azure semy of fleurs-de-lys Or</span></div>
                                <div class="demoBlazon">Per pale Gules and Azure, on a bend sinister between two fleurs-de-lys Or three keys palewise Purpure</div>
                                <div class="demoBlazon">Per pale Azure on a bend between two mullets Or three roundels Vert and Argent three phrygian caps Gules</div>
                                <div class="demoBlazon">Per pale Sable and Or, three roundels counterchanged</div>
                            </div>
                            <p class="menu-item" id="toggleSyntax">Toggle syntax display</p>
                            <a href="https://github.com/qxzkjp" class="menu-item">Github page</a>
                        </div>
                        <div id="versionContainer">Heraldry Engine<br />Version 0.1 (Alpha)<br />By Deus Kane</div>
                    </div>
                </div>
            </div>
		</div>
		<div id="content">
			<hgroup id="mainHead">
				<h1 id="heraldryHead">Log&nbsp;</h1>
				<h2 id="engineHead">In</h2>
			</hgroup>
			<div id="bottomHalf">
			<?php
				if(array_key_exists('username',$_POST))
					echo 'User: ' . $_POST['username'] . '<br>';
				if(array_key_exists('password',$_POST)){
					echo 'Password: ' . $ph . '<br>';
				}
			?>
				<form method="post" action="login.php">
					<input type="text" name="username"></input><br/>
					<input type="password" name="password"></input>
					<input type="submit" name="submit" value="Log in"/>
				</form>
			</div>
		</div>
	</body>
</html>