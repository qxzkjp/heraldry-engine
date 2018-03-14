<?php
	require "session.php";
	if(!array_key_exists('userID',$_SESSION)){
		header('Location: login.php', TRUE, 303);
		exit("tried to redirect");
	}
	require "connect.php";
	$isAdmin=false;
	$passwordChanged=false;
	$passwordError=false;
	if(array_key_exists("accessLevel",$_SESSION) && $_SESSION["accessLevel"] == 0)
		$isAdmin=true;
	if(array_key_exists("newPassword",$_POST)
		&& array_key_exists("checkPassword",$_POST)
	){
		$passwordCheck = ($_POST["newPassword"] == $_POST["checkPassword"]);
		$pHash=password_hash($_POST["newPassword"], PASSWORD_DEFAULT);
		if($isAdmin && array_key_exists("ID",$_POST)){
			$changeID=$_POST["ID"];
		}else{
			$changeID=$_SESSION["userID"];
		}
		if($passwordCheck){
			$stmt = $mysqli->prepare("UPDATE users SET pHash = ? WHERE ID = ?");
			$stmt->bind_param("si", $pHash, $changeID);
			$stmt->execute();
			$stmt->close();
			$passwordChanged=true;
		}else{
			$passwordError=true;
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
		<script type="text/javascript" src="scripts/jquery-3.2.1.min.js"></script>
		<script type="text/javascript" src="scripts/menu.js"></script>
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
						<?php if($_SESSION['accessLevel']==0): ?>
							<a href="admin.php" class="menu-item">Secret admin shit</a>
						<?php else: ?>
							<a href="index.php" class="menu-item">Back to blazonry</a>
						<?php endif ?>
							<a href="logout.php" class="menu-item">Log out</a>
                        </div>
                        <div id="versionContainer">Heraldry Engine<br />Version 0.1 (Alpha)<br />By Deus Kane</div>
                    </div>
                </div>
            </div>
		</div>
		<div id="content">
			<hgroup id="mainHead">
				<h1 id="heraldryHead">Change&nbsp;</h1>
				<h2 id="engineHead">Password</h2>
			</hgroup>
			<div id="bottomHalf">
				<form action="changepassword.php" method="post">
				<?php
					if($isAdmin && array_key_exists("ID",$_POST)){
						echo "<input type='hidden' name='ID' value=" . $_POST["ID"] . "></input>";
					}
				?>
				<?php if($passwordChanged): ?>
					<p>Password changed successfully!</p>
				<?php elseif($passwordError): ?>
					<p style="color:red">Could not set password.</p>
				<?php endif ?>
				<p>
				<?php if(array_key_exists("ID", $_POST)): ?>
				Changing password for user ID <?php echo $_POST["ID"] ?></p>
				<?php else: ?>
				Changing password for your account
				<?php endif ?>
				</p>
					<table>
						<tr>
							<td>New password</td>
							<td><input type="password" name="newPassword"></td>
						</tr>
						<tr>
							<td>Confirm password</td>
							<td><input type="password" name="checkPassword"></td>
						</tr>
					</table>
					<input type="submit" name="submit" value="Change password"/>
				</form>
			</div>
		</div>
	</body>
</html>
