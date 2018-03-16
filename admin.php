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
	$promoteError=false;
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
		}else if($_POST["action"]=="garbageCollect"){
			$handler->gc(ini_get('session.gc_maxlifetime'));
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
		<script type="text/javascript" src="scripts/enable.js"></script>
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
							<a href="index.php" class="menu-item">Back to blazonry</a>
							<a href="createuser.php" class="menu-item">Create new user</a>
							<a href="logout.php" class="menu-item">Log out</a>
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
				<h3>Users</h3>
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
						$users=array();
						while($row = $result->fetch_assoc()) {
							$status;
								if((int)$row['accessLevel'] == 0)
									$status="admin";
								elseif ((int)$row['accessLevel'] == 1)
									$status="standard";
								elseif ((int)$row['accessLevel'] == 2)
									$status="disabled";
								else
									$status="unknown";
							$users[(int)$row["ID"]]=$row["userName"];
						?>
							<tr>
								<td><?php echo $row['ID']; ?></td>
								<td><?php echo $row['userName']; ?></td>
								<td><?php echo $status; ?></td>
								<td><a href="#"
									onclick="post('changepassword.php',{'ID': '<?php echo $row['ID'] ?>'})"
									>Change password</a>
								</td>
								<td><a href="#"
									onclick="post('admin.php',{'action' : 'deleteUser', 'ID': '<?php echo $row['ID'] ?>'})"
									>Delete user</a>
								</td>
								<td>
								<?php if($row["accessLevel"]!=0): ?>
									<a href="#"
										onclick="post('admin.php',{'action' : 'promoteUser', 'ID': '<?php echo $row['ID'] ?>'})"
										>Promote user</a>
								<?php else: ?>
										Promote user
								<?php endif ?>
								</td>
								<td>
								<?php if($row["accessLevel"]!=1): ?>
									<a href="#"
										onclick="post('admin.php',{'action' : 'demoteUser', 'ID': '<?php echo $row['ID'] ?>'})"
										>Demote user</a>
								<?php else: ?>
										Demote user
								<?php endif ?>
								</td>
								<td>
								<?php if($row["accessLevel"]!=2): ?>
									<a href="#"
										onclick="post('admin.php',{'action' : 'disableUser', 'ID': '<?php echo $row['ID'] ?>'})"
										>Disable user</a>
								<?php else: ?>
										Disable user
								<?php endif ?>
								</td>
							</tr>
					<?php
						}
					?>
				</table>
				<h3>Authenticated sessions</h3>
				<?php
					$sesh = $handler->get_all();
				?>
				<table>
					<tr>
						<th>Session ID</th>
						<th>User name</th>
						<th>User IP</th>
						<th>Time created</th>
						<th>Time expires</th>
						<th></th>
					</tr>
					<?php
					foreach($sesh as $id => $data){
						if(array_key_exists("userID",$data)){
							if(!array_key_exists("expiry",$data)
								|| $data["expiry"] >= time()){
					?>
					<tr>
						<td>
						<?php echo $id; ?>
						</td>
						<td>
						<?php
						if(array_key_exists("userID",$data)){
							echo $users[$data["userID"]];
						}
						?>
						</td>
						<td>
						<?php
							if(array_key_exists("userIP",$data)){
								echo $data["userIP"];
							}else{
								echo "unknown";
							}
						?>
						</td>
						<td>
						<?php
							if(array_key_exists("startTime",$data)){
								date_default_timezone_set('Europe/London');
								echo date('d/m/Y H:i:s', $data["startTime"]);
							}
						?>
						</td>
						<td>
						<?php
							if(array_key_exists("expiry",$data)){
								date_default_timezone_set('Europe/London');
								echo date('d/m/Y H:i:s', $data["expiry"]);
							}else{
								echo "Never";
							}
						?>
						</td>
						<td>Delete session</td>
					</tr>
					<?php
							}
						}
					}
					?>
				</table>
				<p>
					<a href="#"
						onclick="post('admin.php',{'action' : 'garbageCollect'})"
						>Collect garbage</a>
				</p>
			</div>
		</div>
	</body>
</html>