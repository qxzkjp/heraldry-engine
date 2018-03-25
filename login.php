<?php
	require "utility/session.php";
	require "utility/useragent.php";
	require "utility/connect.php";
	$uname="";
	$pword="";
	$loginAttempted=(array_key_exists('username',$_POST) || array_key_exists('password',$_POST));
	$rateLimited = false;
	if(array_key_exists('username',$_POST)){
		$uname=strtolower($_POST['username']);
		$stmt = $mysqli->prepare(
			"SELECT COUNT(*) FROM failureLogs WHERE userName=? AND accessTime > (NOW() - INTERVAL 5 MINUTE);");
		$stmt->bind_param("s", $uname);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_row();
		$attempts = $row[0];
		$stmt->close();
		if($attempts < 50){
			if(array_key_exists('password',$_POST)){
				$ph=password_hash($_POST['password'], PASSWORD_DEFAULT);
				$pword=$_POST['password'];
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
					'pHash' => $row['pHash'],
					'accessLevel' => $row['accessLevel']);
			}
			$stmt->close();
			foreach($ids as $value){
				if(password_verify($pword,$value['pHash'])){
					if($value['accessLevel']!=2){
						$_SESSION['userID']=(int)$value['ID'];
						$_SESSION['accessLevel']=(int)$value['accessLevel'];
						$_SESSION["startTime"]=time();
						$_SESSION["userIP"]=$_SERVER["REMOTE_ADDR"];
						$_SESSION["OS"]=getOS();
						$_SESSION["browser"]=getBrowser();
						//get geolocation data from freegeoip (and drop line break)
						$_SESSION["geoIP"] = substr(file_get_contents(
							"https://freegeoip.net/csv/".$_SESSION['userIP']
							), 0, -2);
						$sections=explode(",",$_SESSION["geoIP"]);
						if($sections[2]!=""){
							$_SESSION["countryName"]=$sections[2];
						}
						if($sections[5]!=""){
							$_SESSION["city"]=$sections[5];
						}
						//IP,CountryCode,CountryName,RegionCode,RegionName,City,
						//ZipCode,TimeZone,Latitude,Longitude,MetroCode
						header('Location: index.php', TRUE, 303);
						die();
					}
				}else{
					$stmt = $mysqli->prepare(
						"INSERT INTO failureLogs (userName, accessTime, IP, isIPv6) VALUES (?, NOW(), INET6_ATON(?), IS_IPV6(?));");
					$stmt->bind_param("iss", $uname, $_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_ADDR']);
					$stmt->execute();
				}
			}
		} else {
			$rateLimited = true;
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
			<?php if($rateLimited): ?>
				<p style="color:red">You are rate limited. Chillax, bruh.</p>
			<?php elseif($loginAttempted): ?>
				<p style="color:red">Wrong username or password</p>
			<?php endif ?>
				<form method="post" action="login.php">
					<input type="text" name="username"></input><br/>
					<input type="password" name="password"></input>
					<input type="submit" name="submit" value="Log in"/>
				</form>
			</div>
		</div>
	</body>
</html>
