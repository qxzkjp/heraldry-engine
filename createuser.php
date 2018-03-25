<?php
	require "utility/session.php";
	if(!array_key_exists("accessLevel",$_SESSION) || $_SESSION["accessLevel"]!=0){
		die("Fuck off, Chris.");
	}
	$userCreated=false;
	$passwordError=false;
	$otherError=false;
	if(array_key_exists("newUser", $_POST)){
		$username=strtolower($_POST["newUser"]);
		if( array_key_exists("newPassword", $_POST)
			&& array_key_exists("checkPassword", $_POST) ){
			if($_POST["newPassword"]==$_POST["checkPassword"]){
				$pHash=password_hash($_POST["newPassword"],PASSWORD_DEFAULT);
				$al=1;
				if(array_key_exists("asAdmin",$_POST)){
					$al=0;
				}
				require "connect.php";
				$stmt = $mysqli->prepare("INSERT INTO users (userName, pHash, accessLevel) VALUES (?, ?, ?);");
				$stmt->bind_param("ssi", $username, $pHash, $al);
				$err=false;
				try{
					$err = $stmt->execute();
				}catch(Exception $e) {
					error_log($e->getMessage());
					$otherError=true;
				}
				if(false === $err){
					$otherError=true;
				}else if($otherError == false){
					$userCreated=true;
				}
				$stmt->close();
			}else{
				$passwordError=true;
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
		<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<link href="styles/style.css" rel="stylesheet" type="text/css">
		<link href="styles/narrow.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="scripts/jquery-3.2.1.min.js"></script>
		<script type="text/javascript" src="scripts/ui.js"></script>
		<script type="text/javascript" src="scripts/enable.js"></script>
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
							<a href="admin.php" class="menu-item">Secret admin shit</a>
							<a href="logout.php" class="menu-item">Log out</a>
                        </div>
                        <div id="versionContainer">Heraldry Engine<br />Version 0.1 (Alpha)<br />By Deus Kane</div>
                    </div>
                </div>
            </div>
		</div>
		<div id="content">
			<hgroup id="mainHead">
				<h1 id="heraldryHead">Create&nbsp;</h1>
				<h2 id="engineHead">User</h2>
			</hgroup>
			<div id="bottomHalf">
			<?php if($userCreated): ?>
			<p>User created successfully!</p>
			<?php elseif($passwordError): ?>
			<p>Passwords did not match.</p>
			<?php elseif($otherError): ?>
			<p>Could not create entry (username taken?)</p>
			<?php endif ?>
				<form action="createuser.php" method="post">
					<table>
						<tr>
							<td>User name</td>
							<td><input type="text" name="newUser"></td>
						</tr>
						<tr>
							<td>Password</td>
							<td><input type="password" name="newPassword"></td>
						</tr>
						<tr>
							<td>Confirm password</td>
							<td><input type="password" name="checkPassword"></td>
						</tr>
						<tr>
							<td>Create as admin</td>
							<td><input type="checkbox" name="asAdmin" value="true"></td>
						</tr>
					</table>
					<input type="submit" name="submit" value="Create user"/>
				</form>
			</div>
		</div>
	</body>
</html>
