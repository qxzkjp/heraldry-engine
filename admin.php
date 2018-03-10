<?php
	require 'session.php';
	if(!array_key_exists("accessLevel",$_SESSION) || $_SESSION["accessLevel"]!=0){
		die("Fuck off, Chris.");
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
				<table>
					<tr>
						<th>ID</th>
						<th>username</th>
						<th>status</th>
					</tr>
					<?php
						require "connect.php";
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
								echo '</tr>';
						}
					?>
				</table>
			</div>
		</div>
	</body>
</html>