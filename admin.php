<?php
	require 'session.php';
	if(!array_key_exists("accessLevel",$_SESSION) || $_SESSION["accessLevel"]!=0){
		die("Fuck off, Chris.");
	}
	require "connect.php";
	$userDeleted=false;
	$deleteError=false;
	$userDisabled=false;
	$disableError=false;
	$userDemoted=false;
	$demoteError=false;
	$userPromoted=false;
	$promoteError=true;
	if(array_key_exists("action",$_POST)){
		//do action
		if($_POST["action"]=="deleteUser" && array_key_exists("ID",$_POST)){
			//delete user
			$stmt = $mysqli->prepare("DELETE FROM users WHERE ID = ?;");
			$stmt->bind_param("i", $_POST["ID"]);
			$err = $stmt->execute();
			if(false === $err){
				$deleteError=true;
			}else{
				$userDeleted=true;
			}
			$stmt->close();
		}else if($_POST["action"]=="disableUser"){
			//make disabled
			$stmt = $mysqli->prepare("UPDATE users SET accessLevel=2 WHERE ID = ?;");
			$stmt->bind_param("i", $_POST["ID"]);
			$err = $stmt->execute();
			if(false === $err){
				$disableError=true;
			}else{
				$userDisabled=true;
			}
			$stmt->close();
		}else if($_POST["action"]=="demoteUser"){
			//make standard
			$stmt = $mysqli->prepare("UPDATE users SET accessLevel=1 WHERE ID = ?;");
			$stmt->bind_param("i", $_POST["ID"]);
			$err = $stmt->execute();
			if(false === $err){
				$demoteError=true;
			}else{
				$userDemoted=true;
			}
			$stmt->close();
		}else if($_POST["action"]=="promoteUser"){
			//make admin
			$stmt = $mysqli->prepare("UPDATE users SET accessLevel=0 WHERE ID = ?;");
			$stmt->bind_param("i", $_POST["ID"]);
			$err = $stmt->execute();
			if(false === $err){
				$promoteError=true;
			}else{
				$userPromoted=true;
			}
			$stmt->close();
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
		<script type="text/javascript" src="scripts/ui.js"></script>
		<script type="text/javascript">
			//https://stackoverflow.com/a/133997/1543262
			function post(path, params, method) {
				method = method || "post"; // Set method to post by default if not specified.

				// The rest of this code assumes you are not using a library.
				// It can be made less wordy if you use one.
				var form = document.createElement("form");
				form.setAttribute("method", method);
				form.setAttribute("action", path);

				for(var key in params) {
					if(params.hasOwnProperty(key)) {
						var hiddenField = document.createElement("input");
						hiddenField.setAttribute("type", "hidden");
						hiddenField.setAttribute("name", key);
						hiddenField.setAttribute("value", params[key]);

						form.appendChild(hiddenField);
					}
				}

				document.body.appendChild(form);
				form.submit();
			}
			//example:
			//post('/contact/', {name: 'Johnny Bravo'});
		</script>
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
				<h1 id="heraldryHead">Secret Admin Shit</h1>
			</hgroup>
			<div id="bottomHalf" style="font-family:serif">
			<?php if($userDeleted): ?>
			<p>User ID <?php echo $_POST["ID"]; ?> deleted successfuly.</p>
			<?php elseif($deleteError): ?>
			<p>Failed to delete user.</p>
			<?php endif ?>
			<?php if($userDisabled): ?>
			<p>User ID <?php echo $_POST["ID"]; ?> disabled successfuly.</p>
			<?php elseif($disableError): ?>
			<p>Failed to disable user.</p>
			<?php endif ?>
			<?php if($userDemoted): ?>
			<p>User ID <?php echo $_POST["ID"]; ?> demoted successfuly.</p>
			<?php elseif($demoteError): ?>
			<p>Failed to demote user.</p>
			<?php endif ?>
			<?php if($userPromoted): ?>
			<p>User ID <?php echo $_POST["ID"]; ?> promoted successfuly.</p>
			<?php elseif($promoteError): ?>
			<p>Failed to promote user.</p>
			<?php endif ?>
				<table>
					<tr>
						<th>ID</th>
						<th>username</th>
						<th>status</th>
					</tr>
					<?php
						$stmt = $mysqli->prepare("SELECT * FROM users");
						$stmt->execute();
						$result = $stmt->get_result();
						while($row = $result->fetch_assoc()) {
								echo '<tr>';
								echo '<td>' . $row['ID'] . '</td>';
								echo '<td>' . $row['userName'] . '</td>';
								$status;
								if((int)$row['accessLevel'] == 0)
									$status="admin";
								elseif ((int)$row['accessLevel'] == 1)
									$status="standard";
								elseif ((int)$row['accessLevel'] == 2)
									$status="disabled";
								else
									$status="unknown";
								echo '<td>' . $status . '</td>';
								$onClick="post('changepassword.php',{'ID':" . $row['ID'] . "})";
								echo '<td><a href="#" onclick="'.$onClick.'">Change password</a>';
								$onClick="post('admin.php',{'action' : 'deleteUser', 'ID':" . $row['ID'] . "})";
								echo '<td><a href="#" onclick="'.$onClick.'">Delete user</a>';
								$onClick="post('admin.php',{'action' : 'disableUser', 'ID':" . $row['ID'] . "})";
								echo '<td><a href="#" onclick="'.$onClick.'">Disable user</a>';
								echo '</tr>';
						}
					?>
				</table>
				<p>
					<a href="createuser.php">Create new user</a><br />
					<a href="index.php">Back to blazonry</a>
				</p>
			</div>
		</div>
	</body>
</html>