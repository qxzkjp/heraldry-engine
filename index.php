<?php
  require "session.php";
  if(!array_key_exists('userID',$_SESSION)){
	  header('Location: login.php', TRUE, 303);
	  exit("tried to redirect");
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
		<link id="heraldry-css" href="styles/heraldry.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="path-data-polyfill.js/path-data-polyfill.js"></script>
		<script type="text/javascript" src="scripts/jquery-3.2.1.min.js"></script>
		<!--script type="text/javascript" src="../base64-js/base64js.min.js"></!--script-->
		<script type="text/javascript" src="../TextEncoderLite/index.js"></script>
		<script type="text/javascript" src="scripts/cubic.js"></script>
		<script type="text/javascript" src="scripts/syntax.js"></script>
        <script type="text/javascript" src="scripts/svg.js"></script>
		<script type="text/javascript" src="scripts/ui.js"></script>
		<script type="text/javascript" src="scripts/menu.js"></script>
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
							<?php if($_SESSION['accessLevel']==0): ?>
							<a href="admin.php" class="menu-item">Secret admin shit</a>
							<?php else: ?>
							<a href="changepassword.php" class="menu-item">Change password</a>
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
				<h1 id="heraldryHead">Heraldry&nbsp;</h1>
				<h2 id="engineHead">Engine</h2>
			</hgroup>
			<div style="text-align:center;overflow: auto;" id="dotContainer">
				<svg version="1.1" baseProfile="full" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" id="escutcheonContainer" class="heraldry-colour">
					<defs id="SVGDefs">
						<!-- this is dotty(25, 16, 0.25) -->
						<pattern id="or-bw" width="25" height="25" patternUnits="userSpaceOnUse">
							<rect width="25" height="25" fill="white" />
							<g>
								<ellipse cx="0" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="1.5625" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.125" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="4.6875" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="6.25" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.8125" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="9.375" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.9375" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="12.5" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.0625" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="15.625" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.1875" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="18.75" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="20.3125" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.875" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="23.4375" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="25" cy="0" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0.78125" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="2.34375" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.90625" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="5.46875" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.03125" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="8.59375" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.15625" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="11.71875" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="13.28125" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.84375" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="16.40625" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.96875" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="19.53125" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.09375" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="22.65625" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="24.21875" cy="1.5625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="1.5625" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.125" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="4.6875" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="6.25" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.8125" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="9.375" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.9375" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="12.5" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.0625" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="15.625" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.1875" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="18.75" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="20.3125" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.875" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="23.4375" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="25" cy="3.125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0.78125" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="2.34375" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.90625" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="5.46875" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.03125" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="8.59375" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.15625" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="11.71875" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="13.28125" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.84375" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="16.40625" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.96875" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="19.53125" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.09375" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="22.65625" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="24.21875" cy="4.6875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="1.5625" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.125" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="4.6875" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="6.25" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.8125" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="9.375" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.9375" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="12.5" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.0625" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="15.625" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.1875" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="18.75" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="20.3125" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.875" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="23.4375" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="25" cy="6.25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0.78125" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="2.34375" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.90625" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="5.46875" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.03125" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="8.59375" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.15625" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="11.71875" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="13.28125" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.84375" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="16.40625" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.96875" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="19.53125" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.09375" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="22.65625" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="24.21875" cy="7.8125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="1.5625" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.125" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="4.6875" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="6.25" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.8125" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="9.375" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.9375" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="12.5" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.0625" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="15.625" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.1875" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="18.75" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="20.3125" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.875" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="23.4375" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="25" cy="9.375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0.78125" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="2.34375" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.90625" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="5.46875" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.03125" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="8.59375" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.15625" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="11.71875" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="13.28125" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.84375" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="16.40625" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.96875" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="19.53125" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.09375" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="22.65625" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="24.21875" cy="10.9375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="1.5625" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.125" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="4.6875" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="6.25" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.8125" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="9.375" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.9375" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="12.5" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.0625" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="15.625" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.1875" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="18.75" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="20.3125" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.875" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="23.4375" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="25" cy="12.5" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0.78125" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="2.34375" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.90625" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="5.46875" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.03125" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="8.59375" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.15625" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="11.71875" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="13.28125" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.84375" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="16.40625" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.96875" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="19.53125" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.09375" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="22.65625" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="24.21875" cy="14.0625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="1.5625" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.125" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="4.6875" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="6.25" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.8125" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="9.375" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.9375" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="12.5" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.0625" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="15.625" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.1875" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="18.75" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="20.3125" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.875" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="23.4375" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="25" cy="15.625" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0.78125" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="2.34375" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.90625" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="5.46875" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.03125" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="8.59375" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.15625" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="11.71875" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="13.28125" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.84375" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="16.40625" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.96875" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="19.53125" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.09375" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="22.65625" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="24.21875" cy="17.1875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="1.5625" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.125" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="4.6875" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="6.25" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.8125" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="9.375" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.9375" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="12.5" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.0625" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="15.625" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.1875" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="18.75" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="20.3125" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.875" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="23.4375" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="25" cy="18.75" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0.78125" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="2.34375" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.90625" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="5.46875" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.03125" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="8.59375" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.15625" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="11.71875" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="13.28125" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.84375" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="16.40625" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.96875" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="19.53125" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.09375" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="22.65625" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="24.21875" cy="20.3125" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="1.5625" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.125" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="4.6875" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="6.25" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.8125" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="9.375" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.9375" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="12.5" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.0625" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="15.625" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.1875" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="18.75" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="20.3125" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.875" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="23.4375" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="25" cy="21.875" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0.78125" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="2.34375" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.90625" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="5.46875" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.03125" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="8.59375" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.15625" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="11.71875" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="13.28125" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.84375" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="16.40625" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.96875" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="19.53125" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.09375" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="22.65625" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="24.21875" cy="23.4375" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="0" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="1.5625" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="3.125" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="4.6875" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="6.25" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="7.8125" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="9.375" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="10.9375" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="12.5" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="14.0625" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="15.625" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="17.1875" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="18.75" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="20.3125" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="21.875" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="23.4375" cy="25" rx="0.25" ry="0.25" fill="black"/>
								<ellipse cx="25" cy="25" rx="0.25" ry="0.25" fill="black"/>
							</g>
						</pattern>
						<pattern id="argent-bw" width="200" height="200" patternUnits="userSpaceOnUse" patternTransform="scale(0.0625)">
							<rect width="200" height="200" fill="white" />
						</pattern>
						<!--this is elem=crossHatch(25, 16, 0.25, false, true)-->
						<pattern id="azure-bw" width="25" height="25" patternUnits="userSpaceOnUse">
							<rect width="25" height="25" fill="white" />
							<g stroke="black" stroke-width="0.25" stroke-linecap="square">
								<line x1="0" x2="25" y1="0.78125" y2="0.78125"/>
								<line x1="0" x2="25" y1="2.34375" y2="2.34375"/>
								<line x1="0" x2="25" y1="3.90625" y2="3.90625"/>
								<line x1="0" x2="25" y1="5.46875" y2="5.46875"/>
								<line x1="0" x2="25" y1="7.03125" y2="7.03125"/>
								<line x1="0" x2="25" y1="8.59375" y2="8.59375"/>
								<line x1="0" x2="25" y1="10.15625" y2="10.15625"/>
								<line x1="0" x2="25" y1="11.71875" y2="11.71875"/>
								<line x1="0" x2="25" y1="13.28125" y2="13.28125"/>
								<line x1="0" x2="25" y1="14.84375" y2="14.84375"/>
								<line x1="0" x2="25" y1="16.40625" y2="16.40625"/>
								<line x1="0" x2="25" y1="17.96875" y2="17.96875"/>
								<line x1="0" x2="25" y1="19.53125" y2="19.53125"/>
								<line x1="0" x2="25" y1="21.09375" y2="21.09375"/>
								<line x1="0" x2="25" y1="22.65625" y2="22.65625"/>
								<line x1="0" x2="25" y1="24.21875" y2="24.21875"/>
							</g>
						</pattern>
						<!--this is elem=crossHatch(25, 16, 0.25, true, false)-->
						<pattern id="gules-bw" width="25" height="25" patternUnits="userSpaceOnUse">
							<rect width="25" height="25" fill="white" />
							<g stroke="black" stroke-width="0.25" stroke-linecap="square">
								<line x1="0.78125" x2="0.78125" y1="0" y2="25"/>
								<line x1="2.34375" x2="2.34375" y1="0" y2="25"/>
								<line x1="3.90625" x2="3.90625" y1="0" y2="25"/>
								<line x1="5.46875" x2="5.46875" y1="0" y2="25"/>
								<line x1="7.03125" x2="7.03125" y1="0" y2="25"/>
								<line x1="8.59375" x2="8.59375" y1="0" y2="25"/>
								<line x1="10.15625" x2="10.15625" y1="0" y2="25"/>
								<line x1="11.71875" x2="11.71875" y1="0" y2="25"/>
								<line x1="13.28125" x2="13.28125" y1="0" y2="25"/>
								<line x1="14.84375" x2="14.84375" y1="0" y2="25"/>
								<line x1="16.40625" x2="16.40625" y1="0" y2="25"/>
								<line x1="17.96875" x2="17.96875" y1="0" y2="25"/>
								<line x1="19.53125" x2="19.53125" y1="0" y2="25"/>
								<line x1="21.09375" x2="21.09375" y1="0" y2="25"/>
								<line x1="22.65625" x2="22.65625" y1="0" y2="25"/>
								<line x1="24.21875" x2="24.21875" y1="0" y2="25"/>
							</g>
						</pattern>
						<!--this is elem=crossHatch(25, 10, 0.25, true, false)-->
						<pattern id="vert-bw" width="25" height="25" patternUnits="userSpaceOnUse">
							<rect width="25" height="25" fill="white" />
                            <g stroke="black" stroke-width="0.25" stroke-linecap="square">
                                <line x1="0" x2="25" y1="0" y2="25" />
                                <line x1="2.5" x2="27.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="2.5" y2="27.5" />
                                <line x1="5" x2="30" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="5" y2="30" />
                                <line x1="7.5" x2="32.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="7.5" y2="32.5" />
                                <line x1="10" x2="35" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="10" y2="35" />
                                <line x1="12.5" x2="37.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="12.5" y2="37.5" />
                                <line x1="15" x2="40" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="15" y2="40" />
                                <line x1="17.5" x2="42.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="17.5" y2="42.5" />
                                <line x1="20" x2="45" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="20" y2="45" />
                                <line x1="22.5" x2="47.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="22.5" y2="47.5" />
                                <line x1="25" x2="50" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="25" y2="50" />
                            </g>
						</pattern>
						<!--this is elem=diagonalCrossHatch(25, 10, 0.25)-->
						<pattern id="purpure-bw" width="25" height="25" patternUnits="userSpaceOnUse">
							<rect width="25" height="25" fill="white" />
                            <g stroke="black" stroke-width="0.25" stroke-linecap="square">
                                <line x1="0" x2="25" y1="25" y2="0" />
                                <line x1="2.5" x2="27.5" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="22.5" y2="-2.5" />
                                <line x1="5" x2="30" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="20" y2="-5" />
                                <line x1="7.5" x2="32.5" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="17.5" y2="-7.5" />
                                <line x1="10" x2="35" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="15" y2="-10" />
                                <line x1="12.5" x2="37.5" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="12.5" y2="-12.5" />
                                <line x1="15" x2="40" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="10" y2="-15" />
                                <line x1="17.5" x2="42.5" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="7.5" y2="-17.5" />
                                <line x1="20" x2="45" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="5" y2="-20" />
                                <line x1="22.5" x2="47.5" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="2.5" y2="-22.5" />
                                <line x1="25" x2="50" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="0" y2="-25" />
                            </g>
						</pattern>
						<!-- this is crossHatch(25,8,0.5) -->
						<pattern id="sable-bw" width="25" height="25" patternUnits="userSpaceOnUse">
							<rect width="25" height="25" fill="white" />
							<g stroke="black" stroke-width="0.25" stroke-linecap="square">
								<line x1="0.78125" x2="0.78125" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="0.78125" y2="0.78125"/>
								<line x1="2.34375" x2="2.34375" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="2.34375" y2="2.34375"/>
								<line x1="3.90625" x2="3.90625" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="3.90625" y2="3.90625"/>
								<line x1="5.46875" x2="5.46875" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="5.46875" y2="5.46875"/>
								<line x1="7.03125" x2="7.03125" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="7.03125" y2="7.03125"/>
								<line x1="8.59375" x2="8.59375" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="8.59375" y2="8.59375"/>
								<line x1="10.15625" x2="10.15625" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="10.15625" y2="10.15625"/>
								<line x1="11.71875" x2="11.71875" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="11.71875" y2="11.71875"/>
								<line x1="13.28125" x2="13.28125" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="13.28125" y2="13.28125"/>
								<line x1="14.84375" x2="14.84375" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="14.84375" y2="14.84375"/>
								<line x1="16.40625" x2="16.40625" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="16.40625" y2="16.40625"/>
								<line x1="17.96875" x2="17.96875" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="17.96875" y2="17.96875"/>
								<line x1="19.53125" x2="19.53125" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="19.53125" y2="19.53125"/>
								<line x1="21.09375" x2="21.09375" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="21.09375" y2="21.09375"/>
								<line x1="22.65625" x2="22.65625" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="22.65625" y2="22.65625"/>
								<line x1="24.21875" x2="24.21875" y1="0" y2="25"/>
								<line x1="0" x2="25" y1="24.21875" y2="24.21875"/>
							</g>
						</pattern>
                        <!-- this is gules + vert -->
                        <pattern id="brown-bw" width="25" height="25" patternUnits="userSpaceOnUse">
                            <rect width="25" height="25" fill="white" />
                            <g stroke="black" stroke-width="0.25" stroke-linecap="square">
                                <line x1="0" x2="25" y1="0" y2="25" />
                                <line x1="2.5" x2="27.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="2.5" y2="27.5" />
                                <line x1="5" x2="30" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="5" y2="30" />
                                <line x1="7.5" x2="32.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="7.5" y2="32.5" />
                                <line x1="10" x2="35" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="10" y2="35" />
                                <line x1="12.5" x2="37.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="12.5" y2="37.5" />
                                <line x1="15" x2="40" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="15" y2="40" />
                                <line x1="17.5" x2="42.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="17.5" y2="42.5" />
                                <line x1="20" x2="45" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="20" y2="45" />
                                <line x1="22.5" x2="47.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="22.5" y2="47.5" />
                                <line x1="25" x2="50" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="25" y2="50" />
                            </g>
                            <g stroke="black" stroke-width="0.25" stroke-linecap="square">
                                <line x1="0.78125" x2="0.78125" y1="0" y2="25" />
                                <line x1="2.34375" x2="2.34375" y1="0" y2="25" />
                                <line x1="3.90625" x2="3.90625" y1="0" y2="25" />
                                <line x1="5.46875" x2="5.46875" y1="0" y2="25" />
                                <line x1="7.03125" x2="7.03125" y1="0" y2="25" />
                                <line x1="8.59375" x2="8.59375" y1="0" y2="25" />
                                <line x1="10.15625" x2="10.15625" y1="0" y2="25" />
                                <line x1="11.71875" x2="11.71875" y1="0" y2="25" />
                                <line x1="13.28125" x2="13.28125" y1="0" y2="25" />
                                <line x1="14.84375" x2="14.84375" y1="0" y2="25" />
                                <line x1="16.40625" x2="16.40625" y1="0" y2="25" />
                                <line x1="17.96875" x2="17.96875" y1="0" y2="25" />
                                <line x1="19.53125" x2="19.53125" y1="0" y2="25" />
                                <line x1="21.09375" x2="21.09375" y1="0" y2="25" />
                                <line x1="22.65625" x2="22.65625" y1="0" y2="25" />
                                <line x1="24.21875" x2="24.21875" y1="0" y2="25" />
                            </g>
                        </pattern>
                        <!-- this is gules + purpure -->
                        <pattern id="tenny-bw" width="25" height="25" patternUnits="userSpaceOnUse">
                            <rect width="25" height="25" fill="white" />
                            <g stroke="black" stroke-width="0.25" stroke-linecap="square">
                                <line x1="0" x2="25" y1="25" y2="0" />
                                <line x1="2.5" x2="27.5" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="22.5" y2="-2.5" />
                                <line x1="5" x2="30" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="20" y2="-5" />
                                <line x1="7.5" x2="32.5" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="17.5" y2="-7.5" />
                                <line x1="10" x2="35" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="15" y2="-10" />
                                <line x1="12.5" x2="37.5" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="12.5" y2="-12.5" />
                                <line x1="15" x2="40" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="10" y2="-15" />
                                <line x1="17.5" x2="42.5" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="7.5" y2="-17.5" />
                                <line x1="20" x2="45" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="5" y2="-20" />
                                <line x1="22.5" x2="47.5" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="2.5" y2="-22.5" />
                                <line x1="25" x2="50" y1="25" y2="0" />
                                <line x1="0" x2="25" y1="0" y2="-25" />
                            </g>
                            <g stroke="black" stroke-width="0.25" stroke-linecap="square">
                                <line x1="0.78125" x2="0.78125" y1="0" y2="25" />
                                <line x1="2.34375" x2="2.34375" y1="0" y2="25" />
                                <line x1="3.90625" x2="3.90625" y1="0" y2="25" />
                                <line x1="5.46875" x2="5.46875" y1="0" y2="25" />
                                <line x1="7.03125" x2="7.03125" y1="0" y2="25" />
                                <line x1="8.59375" x2="8.59375" y1="0" y2="25" />
                                <line x1="10.15625" x2="10.15625" y1="0" y2="25" />
                                <line x1="11.71875" x2="11.71875" y1="0" y2="25" />
                                <line x1="13.28125" x2="13.28125" y1="0" y2="25" />
                                <line x1="14.84375" x2="14.84375" y1="0" y2="25" />
                                <line x1="16.40625" x2="16.40625" y1="0" y2="25" />
                                <line x1="17.96875" x2="17.96875" y1="0" y2="25" />
                                <line x1="19.53125" x2="19.53125" y1="0" y2="25" />
                                <line x1="21.09375" x2="21.09375" y1="0" y2="25" />
                                <line x1="22.65625" x2="22.65625" y1="0" y2="25" />
                                <line x1="24.21875" x2="24.21875" y1="0" y2="25" />
                            </g>
                        </pattern>
                        <!-- this is azure + vert -->
                        <pattern id="sanguine-bw" width="25" height="25" patternUnits="userSpaceOnUse">
                            <rect width="25" height="25" fill="white" />
                            <g stroke="black" stroke-width="0.25" stroke-linecap="square">
                                <line x1="0" x2="25" y1="0.78125" y2="0.78125" />
                                <line x1="0" x2="25" y1="2.34375" y2="2.34375" />
                                <line x1="0" x2="25" y1="3.90625" y2="3.90625" />
                                <line x1="0" x2="25" y1="5.46875" y2="5.46875" />
                                <line x1="0" x2="25" y1="7.03125" y2="7.03125" />
                                <line x1="0" x2="25" y1="8.59375" y2="8.59375" />
                                <line x1="0" x2="25" y1="10.15625" y2="10.15625" />
                                <line x1="0" x2="25" y1="11.71875" y2="11.71875" />
                                <line x1="0" x2="25" y1="13.28125" y2="13.28125" />
                                <line x1="0" x2="25" y1="14.84375" y2="14.84375" />
                                <line x1="0" x2="25" y1="16.40625" y2="16.40625" />
                                <line x1="0" x2="25" y1="17.96875" y2="17.96875" />
                                <line x1="0" x2="25" y1="19.53125" y2="19.53125" />
                                <line x1="0" x2="25" y1="21.09375" y2="21.09375" />
                                <line x1="0" x2="25" y1="22.65625" y2="22.65625" />
                                <line x1="0" x2="25" y1="24.21875" y2="24.21875" />
                            </g>
                            <g stroke="black" stroke-width="0.25" stroke-linecap="square">
                                <line x1="0" x2="25" y1="0" y2="25" />
                                <line x1="2.5" x2="27.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="2.5" y2="27.5" />
                                <line x1="5" x2="30" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="5" y2="30" />
                                <line x1="7.5" x2="32.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="7.5" y2="32.5" />
                                <line x1="10" x2="35" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="10" y2="35" />
                                <line x1="12.5" x2="37.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="12.5" y2="37.5" />
                                <line x1="15" x2="40" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="15" y2="40" />
                                <line x1="17.5" x2="42.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="17.5" y2="42.5" />
                                <line x1="20" x2="45" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="20" y2="45" />
                                <line x1="22.5" x2="47.5" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="22.5" y2="47.5" />
                                <line x1="25" x2="50" y1="0" y2="25" />
                                <line x1="0" x2="25" y1="25" y2="50" />
                            </g>
                        </pattern>
						<pattern id="ermine" width="100" height="100" patternUnits="userSpaceOnUse" patternTransform="scale(0.25)">
							<rect width="100" height="100" fill="white" />
							<path d="M 53.275904,37.171262 C 53.275904,38.896261 51.875904,40.296261 50.150905,40.296261 C 48.425906,40.296261 47.025907,38.896261 47.025907,37.171262 C 47.025907,35.446262 48.425906,34.046263 50.150905,34.046263 C 51.875904,34.046263 53.275904,35.446262 53.275904,37.171262 Z M 48.775904,42.671262 C 48.775904,44.396261 47.375904,45.796261 45.650904,45.796261 C 43.925905,45.796261 42.525906,44.396261 42.525906,42.671262 C 42.525906,40.946262 43.925905,39.546263 45.650904,39.546263 C 47.375904,39.546263 48.775904,40.946262 48.775904,42.671262 Z M 57.775904,42.671262 C 57.775904,44.396261 56.375904,45.796261 54.650905,45.796261 C 52.925906,45.796261 51.525907,44.396261 51.525907,42.671262 C 51.525907,40.946262 52.925906,39.546263 54.650905,39.546263 C 56.375904,39.546263 57.775904,40.946262 57.775904,42.671262 Z M 49.236555,41.014512 M 50.712549,41.116305 C 50.763446,50.73572 58.092524,65.953737 58.092524,65.953737 L 53.486402,61.5512 C 52.213993,63.027195 49.949104,65.953737 49.949104,65.953737 C 49.949104,65.953737 48.32042,63.001747 47.149803,61.525752 L 41.907477,65.90284 C 41.907477,65.90284 49.134761,50.837513 49.236555,41.014512 Z" fill="black" />
							<path d="M 3.275904,-12.828738 C 3.275904,-11.103739 1.875904,-9.703739 0.150905,-9.703739 C -1.574095,-9.703739 -2.974094,-11.103739 -2.974094,-12.828738 C -2.974094,-14.553738 -1.574095,-15.953737 0.150905,-15.953737 C 1.875904,-15.953737 3.275904,-14.553738 3.275904,-12.828738 Z M -1.224097,-7.328738 C -1.224097,-5.603739 -2.624097,-4.203739 -4.349096,-4.203739 C -6.074095,-4.203739 -7.474094,-5.603739 -7.474094,-7.328738 C -7.474094,-9.053738 -6.074095,-10.453737 -4.349096,-10.453737 C -2.624097,-10.453737 -1.224097,-9.053738 -1.224097,-7.328738 Z M 7.775904,-7.328738 C 7.775904,-5.603739 6.375904,-4.203739 4.650905,-4.203739 C 2.925905,-4.203739 1.525907,-5.603739 1.525907,-7.328738 C 1.525907,-9.053738 2.925905,-10.453737 4.650905,-10.453737 C 6.375904,-10.453737 7.775904,-9.053738 7.775904,-7.328738 Z M -0.763446,-8.985488 M 0.712549,-8.883695 C 0.763446,0.73572 8.092523,15.953737 8.092523,15.953737 L 3.486401,11.5512 C 2.213993,13.027195 -0.050896,15.953737 -0.050896,15.953737 C -0.050896,15.953737 -1.679581,13.001747 -2.850197,11.525752 L -8.092523,15.90284 C -8.092523,15.90284 -0.865239,0.837513 -0.763446,-8.985488 Z" fill="black" />
							<path d="M 103.275904,-12.828738 C 103.275904,-11.103739 101.875904,-9.703739 100.150905,-9.703739 C 98.425906,-9.703739 97.025906,-11.103739 97.025906,-12.828738 C 97.025906,-14.553738 98.425906,-15.953737 100.150905,-15.953737 C 101.875904,-15.953737 103.275904,-14.553738 103.275904,-12.828738 Z M 98.775904,-7.328738 C 98.775904,-5.603739 97.375904,-4.203739 95.650905,-4.203739 C 93.925905,-4.203739 92.525906,-5.603739 92.525906,-7.328738 C 92.525906,-9.053738 93.925905,-10.453737 95.650905,-10.453737 C 97.375904,-10.453737 98.775904,-9.053738 98.775904,-7.328738 Z M 107.775904,-7.328738 C 107.775904,-5.603739 106.375904,-4.203739 104.650905,-4.203739 C 102.925906,-4.203739 101.525907,-5.603739 101.525907,-7.328738 C 101.525907,-9.053738 102.925906,-10.453737 104.650905,-10.453737 C 106.375904,-10.453737 107.775904,-9.053738 107.775904,-7.328738 Z M 99.236555,-8.985488 M 100.712549,-8.883695 C 100.763446,0.73572 108.092524,15.953737 108.092524,15.953737 L 103.486402,11.5512 C 102.213993,13.027195 99.949104,15.953737 99.949104,15.953737 C 99.949104,15.953737 98.32042,13.001747 97.149803,11.525752 L 91.907477,15.90284 C 91.907477,15.90284 99.134762,0.837513 99.236555,-8.985488 Z" fill="black" />
							<path d="M 3.275904,87.171262 C 3.275904,88.896261 1.875904,90.296261 0.150905,90.296261 C -1.574095,90.296261 -2.974094,88.896261 -2.974094,87.171262 C -2.974094,85.446262 -1.574095,84.046263 0.150905,84.046263 C 1.875904,84.046263 3.275904,85.446262 3.275904,87.171262 Z M -1.224097,92.671262 C -1.224097,94.396261 -2.624097,95.796261 -4.349096,95.796261 C -6.074095,95.796261 -7.474094,94.396261 -7.474094,92.671262 C -7.474094,90.946262 -6.074095,89.546263 -4.349096,89.546263 C -2.624097,89.546263 -1.224097,90.946262 -1.224097,92.671262 Z M 7.775904,92.671262 C 7.775904,94.396261 6.375904,95.796261 4.650905,95.796261 C 2.925905,95.796261 1.525907,94.396261 1.525907,92.671262 C 1.525907,90.946262 2.925905,89.546263 4.650905,89.546263 C 6.375904,89.546263 7.775904,90.946262 7.775904,92.671262 Z M -0.763446,91.014512 M 0.712549,91.116305 C 0.763446,100.73572 8.092523,115.953737 8.092523,115.953737 L 3.486401,111.5512 C 2.213993,113.027195 -0.050896,115.953737 -0.050896,115.953737 C -0.050896,115.953737 -1.679581,113.001747 -2.850197,111.525752 L -8.092523,115.90284 C -8.092523,115.90284 -0.865239,100.837513 -0.763446,91.014512 Z" fill="black" />
							<path d="M 103.275904,87.171262 C 103.275904,88.896261 101.875904,90.296261 100.150905,90.296261 C 98.425906,90.296261 97.025906,88.896261 97.025906,87.171262 C 97.025906,85.446262 98.425906,84.046263 100.150905,84.046263 C 101.875904,84.046263 103.275904,85.446262 103.275904,87.171262 Z M 98.775904,92.671262 C 98.775904,94.396261 97.375904,95.796261 95.650905,95.796261 C 93.925905,95.796261 92.525906,94.396261 92.525906,92.671262 C 92.525906,90.946262 93.925905,89.546263 95.650905,89.546263 C 97.375904,89.546263 98.775904,90.946262 98.775904,92.671262 Z M 107.775904,92.671262 C 107.775904,94.396261 106.375904,95.796261 104.650905,95.796261 C 102.925906,95.796261 101.525907,94.396261 101.525907,92.671262 C 101.525907,90.946262 102.925906,89.546263 104.650905,89.546263 C 106.375904,89.546263 107.775904,90.946262 107.775904,92.671262 Z M 99.236555,91.014512 M 100.712549,91.116305 C 100.763446,100.73572 108.092524,115.953737 108.092524,115.953737 L 103.486402,111.5512 C 102.213993,113.027195 99.949104,115.953737 99.949104,115.953737 C 99.949104,115.953737 98.32042,113.001747 97.149803,111.525752 L 91.907477,115.90284 C 91.907477,115.90284 99.134762,100.837513 99.236555,91.014512 Z" fill="black" />
						</pattern>
						<pattern id="ermine-bend" width="100" height="100" patternUnits="userSpaceOnUse" patternTransform="scale(0.25)rotate(-45)">
							<rect width="100" height="100" fill="white" />
							<path d="M 53.275904,37.171262 C 53.275904,38.896261 51.875904,40.296261 50.150905,40.296261 C 48.425906,40.296261 47.025907,38.896261 47.025907,37.171262 C 47.025907,35.446262 48.425906,34.046263 50.150905,34.046263 C 51.875904,34.046263 53.275904,35.446262 53.275904,37.171262 Z M 48.775904,42.671262 C 48.775904,44.396261 47.375904,45.796261 45.650904,45.796261 C 43.925905,45.796261 42.525906,44.396261 42.525906,42.671262 C 42.525906,40.946262 43.925905,39.546263 45.650904,39.546263 C 47.375904,39.546263 48.775904,40.946262 48.775904,42.671262 Z M 57.775904,42.671262 C 57.775904,44.396261 56.375904,45.796261 54.650905,45.796261 C 52.925906,45.796261 51.525907,44.396261 51.525907,42.671262 C 51.525907,40.946262 52.925906,39.546263 54.650905,39.546263 C 56.375904,39.546263 57.775904,40.946262 57.775904,42.671262 Z M 49.236555,41.014512 M 50.712549,41.116305 C 50.763446,50.73572 58.092524,65.953737 58.092524,65.953737 L 53.486402,61.5512 C 52.213993,63.027195 49.949104,65.953737 49.949104,65.953737 C 49.949104,65.953737 48.32042,63.001747 47.149803,61.525752 L 41.907477,65.90284 C 41.907477,65.90284 49.134761,50.837513 49.236555,41.014512 Z" fill="black" />
							<path d="M 3.275904,-12.828738 C 3.275904,-11.103739 1.875904,-9.703739 0.150905,-9.703739 C -1.574095,-9.703739 -2.974094,-11.103739 -2.974094,-12.828738 C -2.974094,-14.553738 -1.574095,-15.953737 0.150905,-15.953737 C 1.875904,-15.953737 3.275904,-14.553738 3.275904,-12.828738 Z M -1.224097,-7.328738 C -1.224097,-5.603739 -2.624097,-4.203739 -4.349096,-4.203739 C -6.074095,-4.203739 -7.474094,-5.603739 -7.474094,-7.328738 C -7.474094,-9.053738 -6.074095,-10.453737 -4.349096,-10.453737 C -2.624097,-10.453737 -1.224097,-9.053738 -1.224097,-7.328738 Z M 7.775904,-7.328738 C 7.775904,-5.603739 6.375904,-4.203739 4.650905,-4.203739 C 2.925905,-4.203739 1.525907,-5.603739 1.525907,-7.328738 C 1.525907,-9.053738 2.925905,-10.453737 4.650905,-10.453737 C 6.375904,-10.453737 7.775904,-9.053738 7.775904,-7.328738 Z M -0.763446,-8.985488 M 0.712549,-8.883695 C 0.763446,0.73572 8.092523,15.953737 8.092523,15.953737 L 3.486401,11.5512 C 2.213993,13.027195 -0.050896,15.953737 -0.050896,15.953737 C -0.050896,15.953737 -1.679581,13.001747 -2.850197,11.525752 L -8.092523,15.90284 C -8.092523,15.90284 -0.865239,0.837513 -0.763446,-8.985488 Z" fill="black" />
							<path d="M 103.275904,-12.828738 C 103.275904,-11.103739 101.875904,-9.703739 100.150905,-9.703739 C 98.425906,-9.703739 97.025906,-11.103739 97.025906,-12.828738 C 97.025906,-14.553738 98.425906,-15.953737 100.150905,-15.953737 C 101.875904,-15.953737 103.275904,-14.553738 103.275904,-12.828738 Z M 98.775904,-7.328738 C 98.775904,-5.603739 97.375904,-4.203739 95.650905,-4.203739 C 93.925905,-4.203739 92.525906,-5.603739 92.525906,-7.328738 C 92.525906,-9.053738 93.925905,-10.453737 95.650905,-10.453737 C 97.375904,-10.453737 98.775904,-9.053738 98.775904,-7.328738 Z M 107.775904,-7.328738 C 107.775904,-5.603739 106.375904,-4.203739 104.650905,-4.203739 C 102.925906,-4.203739 101.525907,-5.603739 101.525907,-7.328738 C 101.525907,-9.053738 102.925906,-10.453737 104.650905,-10.453737 C 106.375904,-10.453737 107.775904,-9.053738 107.775904,-7.328738 Z M 99.236555,-8.985488 M 100.712549,-8.883695 C 100.763446,0.73572 108.092524,15.953737 108.092524,15.953737 L 103.486402,11.5512 C 102.213993,13.027195 99.949104,15.953737 99.949104,15.953737 C 99.949104,15.953737 98.32042,13.001747 97.149803,11.525752 L 91.907477,15.90284 C 91.907477,15.90284 99.134762,0.837513 99.236555,-8.985488 Z" fill="black" />
							<path d="M 3.275904,87.171262 C 3.275904,88.896261 1.875904,90.296261 0.150905,90.296261 C -1.574095,90.296261 -2.974094,88.896261 -2.974094,87.171262 C -2.974094,85.446262 -1.574095,84.046263 0.150905,84.046263 C 1.875904,84.046263 3.275904,85.446262 3.275904,87.171262 Z M -1.224097,92.671262 C -1.224097,94.396261 -2.624097,95.796261 -4.349096,95.796261 C -6.074095,95.796261 -7.474094,94.396261 -7.474094,92.671262 C -7.474094,90.946262 -6.074095,89.546263 -4.349096,89.546263 C -2.624097,89.546263 -1.224097,90.946262 -1.224097,92.671262 Z M 7.775904,92.671262 C 7.775904,94.396261 6.375904,95.796261 4.650905,95.796261 C 2.925905,95.796261 1.525907,94.396261 1.525907,92.671262 C 1.525907,90.946262 2.925905,89.546263 4.650905,89.546263 C 6.375904,89.546263 7.775904,90.946262 7.775904,92.671262 Z M -0.763446,91.014512 M 0.712549,91.116305 C 0.763446,100.73572 8.092523,115.953737 8.092523,115.953737 L 3.486401,111.5512 C 2.213993,113.027195 -0.050896,115.953737 -0.050896,115.953737 C -0.050896,115.953737 -1.679581,113.001747 -2.850197,111.525752 L -8.092523,115.90284 C -8.092523,115.90284 -0.865239,100.837513 -0.763446,91.014512 Z" fill="black" />
							<path d="M 103.275904,87.171262 C 103.275904,88.896261 101.875904,90.296261 100.150905,90.296261 C 98.425906,90.296261 97.025906,88.896261 97.025906,87.171262 C 97.025906,85.446262 98.425906,84.046263 100.150905,84.046263 C 101.875904,84.046263 103.275904,85.446262 103.275904,87.171262 Z M 98.775904,92.671262 C 98.775904,94.396261 97.375904,95.796261 95.650905,95.796261 C 93.925905,95.796261 92.525906,94.396261 92.525906,92.671262 C 92.525906,90.946262 93.925905,89.546263 95.650905,89.546263 C 97.375904,89.546263 98.775904,90.946262 98.775904,92.671262 Z M 107.775904,92.671262 C 107.775904,94.396261 106.375904,95.796261 104.650905,95.796261 C 102.925906,95.796261 101.525907,94.396261 101.525907,92.671262 C 101.525907,90.946262 102.925906,89.546263 104.650905,89.546263 C 106.375904,89.546263 107.775904,90.946262 107.775904,92.671262 Z M 99.236555,91.014512 M 100.712549,91.116305 C 100.763446,100.73572 108.092524,115.953737 108.092524,115.953737 L 103.486402,111.5512 C 102.213993,113.027195 99.949104,115.953737 99.949104,115.953737 C 99.949104,115.953737 98.32042,113.001747 97.149803,111.525752 L 91.907477,115.90284 C 91.907477,115.90284 99.134762,100.837513 99.236555,91.014512 Z" fill="black" />
						</pattern>
						<pattern id="ermine-sinister" width="100" height="100" patternUnits="userSpaceOnUse" patternTransform="scale(0.25)rotate(45)">
							<rect width="100" height="100" fill="white" />
							<path d="M 53.275904,37.171262 C 53.275904,38.896261 51.875904,40.296261 50.150905,40.296261 C 48.425906,40.296261 47.025907,38.896261 47.025907,37.171262 C 47.025907,35.446262 48.425906,34.046263 50.150905,34.046263 C 51.875904,34.046263 53.275904,35.446262 53.275904,37.171262 Z M 48.775904,42.671262 C 48.775904,44.396261 47.375904,45.796261 45.650904,45.796261 C 43.925905,45.796261 42.525906,44.396261 42.525906,42.671262 C 42.525906,40.946262 43.925905,39.546263 45.650904,39.546263 C 47.375904,39.546263 48.775904,40.946262 48.775904,42.671262 Z M 57.775904,42.671262 C 57.775904,44.396261 56.375904,45.796261 54.650905,45.796261 C 52.925906,45.796261 51.525907,44.396261 51.525907,42.671262 C 51.525907,40.946262 52.925906,39.546263 54.650905,39.546263 C 56.375904,39.546263 57.775904,40.946262 57.775904,42.671262 Z M 49.236555,41.014512 M 50.712549,41.116305 C 50.763446,50.73572 58.092524,65.953737 58.092524,65.953737 L 53.486402,61.5512 C 52.213993,63.027195 49.949104,65.953737 49.949104,65.953737 C 49.949104,65.953737 48.32042,63.001747 47.149803,61.525752 L 41.907477,65.90284 C 41.907477,65.90284 49.134761,50.837513 49.236555,41.014512 Z" fill="black" />
							<path d="M 3.275904,-12.828738 C 3.275904,-11.103739 1.875904,-9.703739 0.150905,-9.703739 C -1.574095,-9.703739 -2.974094,-11.103739 -2.974094,-12.828738 C -2.974094,-14.553738 -1.574095,-15.953737 0.150905,-15.953737 C 1.875904,-15.953737 3.275904,-14.553738 3.275904,-12.828738 Z M -1.224097,-7.328738 C -1.224097,-5.603739 -2.624097,-4.203739 -4.349096,-4.203739 C -6.074095,-4.203739 -7.474094,-5.603739 -7.474094,-7.328738 C -7.474094,-9.053738 -6.074095,-10.453737 -4.349096,-10.453737 C -2.624097,-10.453737 -1.224097,-9.053738 -1.224097,-7.328738 Z M 7.775904,-7.328738 C 7.775904,-5.603739 6.375904,-4.203739 4.650905,-4.203739 C 2.925905,-4.203739 1.525907,-5.603739 1.525907,-7.328738 C 1.525907,-9.053738 2.925905,-10.453737 4.650905,-10.453737 C 6.375904,-10.453737 7.775904,-9.053738 7.775904,-7.328738 Z M -0.763446,-8.985488 M 0.712549,-8.883695 C 0.763446,0.73572 8.092523,15.953737 8.092523,15.953737 L 3.486401,11.5512 C 2.213993,13.027195 -0.050896,15.953737 -0.050896,15.953737 C -0.050896,15.953737 -1.679581,13.001747 -2.850197,11.525752 L -8.092523,15.90284 C -8.092523,15.90284 -0.865239,0.837513 -0.763446,-8.985488 Z" fill="black" />
							<path d="M 103.275904,-12.828738 C 103.275904,-11.103739 101.875904,-9.703739 100.150905,-9.703739 C 98.425906,-9.703739 97.025906,-11.103739 97.025906,-12.828738 C 97.025906,-14.553738 98.425906,-15.953737 100.150905,-15.953737 C 101.875904,-15.953737 103.275904,-14.553738 103.275904,-12.828738 Z M 98.775904,-7.328738 C 98.775904,-5.603739 97.375904,-4.203739 95.650905,-4.203739 C 93.925905,-4.203739 92.525906,-5.603739 92.525906,-7.328738 C 92.525906,-9.053738 93.925905,-10.453737 95.650905,-10.453737 C 97.375904,-10.453737 98.775904,-9.053738 98.775904,-7.328738 Z M 107.775904,-7.328738 C 107.775904,-5.603739 106.375904,-4.203739 104.650905,-4.203739 C 102.925906,-4.203739 101.525907,-5.603739 101.525907,-7.328738 C 101.525907,-9.053738 102.925906,-10.453737 104.650905,-10.453737 C 106.375904,-10.453737 107.775904,-9.053738 107.775904,-7.328738 Z M 99.236555,-8.985488 M 100.712549,-8.883695 C 100.763446,0.73572 108.092524,15.953737 108.092524,15.953737 L 103.486402,11.5512 C 102.213993,13.027195 99.949104,15.953737 99.949104,15.953737 C 99.949104,15.953737 98.32042,13.001747 97.149803,11.525752 L 91.907477,15.90284 C 91.907477,15.90284 99.134762,0.837513 99.236555,-8.985488 Z" fill="black" />
							<path d="M 3.275904,87.171262 C 3.275904,88.896261 1.875904,90.296261 0.150905,90.296261 C -1.574095,90.296261 -2.974094,88.896261 -2.974094,87.171262 C -2.974094,85.446262 -1.574095,84.046263 0.150905,84.046263 C 1.875904,84.046263 3.275904,85.446262 3.275904,87.171262 Z M -1.224097,92.671262 C -1.224097,94.396261 -2.624097,95.796261 -4.349096,95.796261 C -6.074095,95.796261 -7.474094,94.396261 -7.474094,92.671262 C -7.474094,90.946262 -6.074095,89.546263 -4.349096,89.546263 C -2.624097,89.546263 -1.224097,90.946262 -1.224097,92.671262 Z M 7.775904,92.671262 C 7.775904,94.396261 6.375904,95.796261 4.650905,95.796261 C 2.925905,95.796261 1.525907,94.396261 1.525907,92.671262 C 1.525907,90.946262 2.925905,89.546263 4.650905,89.546263 C 6.375904,89.546263 7.775904,90.946262 7.775904,92.671262 Z M -0.763446,91.014512 M 0.712549,91.116305 C 0.763446,100.73572 8.092523,115.953737 8.092523,115.953737 L 3.486401,111.5512 C 2.213993,113.027195 -0.050896,115.953737 -0.050896,115.953737 C -0.050896,115.953737 -1.679581,113.001747 -2.850197,111.525752 L -8.092523,115.90284 C -8.092523,115.90284 -0.865239,100.837513 -0.763446,91.014512 Z" fill="black" />
							<path d="M 103.275904,87.171262 C 103.275904,88.896261 101.875904,90.296261 100.150905,90.296261 C 98.425906,90.296261 97.025906,88.896261 97.025906,87.171262 C 97.025906,85.446262 98.425906,84.046263 100.150905,84.046263 C 101.875904,84.046263 103.275904,85.446262 103.275904,87.171262 Z M 98.775904,92.671262 C 98.775904,94.396261 97.375904,95.796261 95.650905,95.796261 C 93.925905,95.796261 92.525906,94.396261 92.525906,92.671262 C 92.525906,90.946262 93.925905,89.546263 95.650905,89.546263 C 97.375904,89.546263 98.775904,90.946262 98.775904,92.671262 Z M 107.775904,92.671262 C 107.775904,94.396261 106.375904,95.796261 104.650905,95.796261 C 102.925906,95.796261 101.525907,94.396261 101.525907,92.671262 C 101.525907,90.946262 102.925906,89.546263 104.650905,89.546263 C 106.375904,89.546263 107.775904,90.946262 107.775904,92.671262 Z M 99.236555,91.014512 M 100.712549,91.116305 C 100.763446,100.73572 108.092524,115.953737 108.092524,115.953737 L 103.486402,111.5512 C 102.213993,113.027195 99.949104,115.953737 99.949104,115.953737 C 99.949104,115.953737 98.32042,113.001747 97.149803,111.525752 L 91.907477,115.90284 C 91.907477,115.90284 99.134762,100.837513 99.236555,91.014512 Z" fill="black" />
						</pattern>
						<pattern id="ermines-bw" width="100" height="100" patternUnits="userSpaceOnUse" patternTransform="scale(0.25)">
							<rect width="100" height="100" fill="white" />
							<line x1="1.25" x2="1.25" y1="0" y2="100" stroke="black" stroke-width="2" stroke-linecap="square"/>
							<line x1="26.25" x2="26.25" y1="0" y2="100" stroke="black" stroke-width="2" stroke-linecap="square"/>
							<line x1="50.25" x2="50.25" y1="0" y2="100" stroke="black" stroke-width="2" stroke-linecap="square"/>
							<line x1="76.25" x2="76.25" y1="0" y2="100" stroke="black" stroke-width="2" stroke-linecap="square"/>
							<line x1="0" x2="100" y1="6.25" y2="6.25" stroke="black" stroke-width="2" stroke-linecap="square"/>
							<line x1="0" x2="100" y1="31.25" y2="31.25" stroke="black" stroke-width="2" stroke-linecap="square"/>
							<line x1="0" x2="100" y1="56.25" y2="56.25" stroke="black" stroke-width="2" stroke-linecap="square"/>
							<line x1="0" x2="100" y1="81.25" y2="81.25" stroke="black" stroke-width="2" stroke-linecap="square"/>
							<path d="M 53.275904,37.171262 C 53.275904,38.896261 51.875904,40.296261 50.150905,40.296261 C 48.425906,40.296261 47.025907,38.896261 47.025907,37.171262 C 47.025907,35.446262 48.425906,34.046263 50.150905,34.046263 C 51.875904,34.046263 53.275904,35.446262 53.275904,37.171262 Z M 48.775904,42.671262 C 48.775904,44.396261 47.375904,45.796261 45.650904,45.796261 C 43.925905,45.796261 42.525906,44.396261 42.525906,42.671262 C 42.525906,40.946262 43.925905,39.546263 45.650904,39.546263 C 47.375904,39.546263 48.775904,40.946262 48.775904,42.671262 Z M 57.775904,42.671262 C 57.775904,44.396261 56.375904,45.796261 54.650905,45.796261 C 52.925906,45.796261 51.525907,44.396261 51.525907,42.671262 C 51.525907,40.946262 52.925906,39.546263 54.650905,39.546263 C 56.375904,39.546263 57.775904,40.946262 57.775904,42.671262 Z M 49.236555,41.014512 M 50.712549,41.116305 C 50.763446,50.73572 58.092524,65.953737 58.092524,65.953737 L 53.486402,61.5512 C 52.213993,63.027195 49.949104,65.953737 49.949104,65.953737 C 49.949104,65.953737 48.32042,63.001747 47.149803,61.525752 L 41.907477,65.90284 C 41.907477,65.90284 49.134761,50.837513 49.236555,41.014512 Z" fill="white" stroke="black" stroke-width="2" />
							<path d="M 3.275904,-12.828738 C 3.275904,-11.103739 1.875904,-9.703739 0.150905,-9.703739 C -1.574095,-9.703739 -2.974094,-11.103739 -2.974094,-12.828738 C -2.974094,-14.553738 -1.574095,-15.953737 0.150905,-15.953737 C 1.875904,-15.953737 3.275904,-14.553738 3.275904,-12.828738 Z M -1.224097,-7.328738 C -1.224097,-5.603739 -2.624097,-4.203739 -4.349096,-4.203739 C -6.074095,-4.203739 -7.474094,-5.603739 -7.474094,-7.328738 C -7.474094,-9.053738 -6.074095,-10.453737 -4.349096,-10.453737 C -2.624097,-10.453737 -1.224097,-9.053738 -1.224097,-7.328738 Z M 7.775904,-7.328738 C 7.775904,-5.603739 6.375904,-4.203739 4.650905,-4.203739 C 2.925905,-4.203739 1.525907,-5.603739 1.525907,-7.328738 C 1.525907,-9.053738 2.925905,-10.453737 4.650905,-10.453737 C 6.375904,-10.453737 7.775904,-9.053738 7.775904,-7.328738 Z M -0.763446,-8.985488 M 0.712549,-8.883695 C 0.763446,0.73572 8.092523,15.953737 8.092523,15.953737 L 3.486401,11.5512 C 2.213993,13.027195 -0.050896,15.953737 -0.050896,15.953737 C -0.050896,15.953737 -1.679581,13.001747 -2.850197,11.525752 L -8.092523,15.90284 C -8.092523,15.90284 -0.865239,0.837513 -0.763446,-8.985488 Z" fill="white" stroke="black" stroke-width="2" />
							<path d="M 103.275904,-12.828738 C 103.275904,-11.103739 101.875904,-9.703739 100.150905,-9.703739 C 98.425906,-9.703739 97.025906,-11.103739 97.025906,-12.828738 C 97.025906,-14.553738 98.425906,-15.953737 100.150905,-15.953737 C 101.875904,-15.953737 103.275904,-14.553738 103.275904,-12.828738 Z M 98.775904,-7.328738 C 98.775904,-5.603739 97.375904,-4.203739 95.650905,-4.203739 C 93.925905,-4.203739 92.525906,-5.603739 92.525906,-7.328738 C 92.525906,-9.053738 93.925905,-10.453737 95.650905,-10.453737 C 97.375904,-10.453737 98.775904,-9.053738 98.775904,-7.328738 Z M 107.775904,-7.328738 C 107.775904,-5.603739 106.375904,-4.203739 104.650905,-4.203739 C 102.925906,-4.203739 101.525907,-5.603739 101.525907,-7.328738 C 101.525907,-9.053738 102.925906,-10.453737 104.650905,-10.453737 C 106.375904,-10.453737 107.775904,-9.053738 107.775904,-7.328738 Z M 99.236555,-8.985488 M 100.712549,-8.883695 C 100.763446,0.73572 108.092524,15.953737 108.092524,15.953737 L 103.486402,11.5512 C 102.213993,13.027195 99.949104,15.953737 99.949104,15.953737 C 99.949104,15.953737 98.32042,13.001747 97.149803,11.525752 L 91.907477,15.90284 C 91.907477,15.90284 99.134762,0.837513 99.236555,-8.985488 Z" fill="white" stroke="black" stroke-width="2" />
							<path d="M 3.275904,87.171262 C 3.275904,88.896261 1.875904,90.296261 0.150905,90.296261 C -1.574095,90.296261 -2.974094,88.896261 -2.974094,87.171262 C -2.974094,85.446262 -1.574095,84.046263 0.150905,84.046263 C 1.875904,84.046263 3.275904,85.446262 3.275904,87.171262 Z M -1.224097,92.671262 C -1.224097,94.396261 -2.624097,95.796261 -4.349096,95.796261 C -6.074095,95.796261 -7.474094,94.396261 -7.474094,92.671262 C -7.474094,90.946262 -6.074095,89.546263 -4.349096,89.546263 C -2.624097,89.546263 -1.224097,90.946262 -1.224097,92.671262 Z M 7.775904,92.671262 C 7.775904,94.396261 6.375904,95.796261 4.650905,95.796261 C 2.925905,95.796261 1.525907,94.396261 1.525907,92.671262 C 1.525907,90.946262 2.925905,89.546263 4.650905,89.546263 C 6.375904,89.546263 7.775904,90.946262 7.775904,92.671262 Z M -0.763446,91.014512 M 0.712549,91.116305 C 0.763446,100.73572 8.092523,115.953737 8.092523,115.953737 L 3.486401,111.5512 C 2.213993,113.027195 -0.050896,115.953737 -0.050896,115.953737 C -0.050896,115.953737 -1.679581,113.001747 -2.850197,111.525752 L -8.092523,115.90284 C -8.092523,115.90284 -0.865239,100.837513 -0.763446,91.014512 Z" fill="white" stroke="black" stroke-width="2" />
							<path d="M 103.275904,87.171262 C 103.275904,88.896261 101.875904,90.296261 100.150905,90.296261 C 98.425906,90.296261 97.025906,88.896261 97.025906,87.171262 C 97.025906,85.446262 98.425906,84.046263 100.150905,84.046263 C 101.875904,84.046263 103.275904,85.446262 103.275904,87.171262 Z M 98.775904,92.671262 C 98.775904,94.396261 97.375904,95.796261 95.650905,95.796261 C 93.925905,95.796261 92.525906,94.396261 92.525906,92.671262 C 92.525906,90.946262 93.925905,89.546263 95.650905,89.546263 C 97.375904,89.546263 98.775904,90.946262 98.775904,92.671262 Z M 107.775904,92.671262 C 107.775904,94.396261 106.375904,95.796261 104.650905,95.796261 C 102.925906,95.796261 101.525907,94.396261 101.525907,92.671262 C 101.525907,90.946262 102.925906,89.546263 104.650905,89.546263 C 106.375904,89.546263 107.775904,90.946262 107.775904,92.671262 Z M 99.236555,91.014512 M 100.712549,91.116305 C 100.763446,100.73572 108.092524,115.953737 108.092524,115.953737 L 103.486402,111.5512 C 102.213993,113.027195 99.949104,115.953737 99.949104,115.953737 C 99.949104,115.953737 98.32042,113.001747 97.149803,111.525752 L 91.907477,115.90284 C 91.907477,115.90284 99.134762,100.837513 99.236555,91.014512 Z" fill="white" stroke="black" stroke-width="2" />
						</pattern>
						<pattern id="ermines" width="100" height="100" patternUnits="userSpaceOnUse" patternTransform="scale(0.25)">
							<rect width="100" height="100" fill="black" />
							<path d="M 53.275904,37.171262 C 53.275904,38.896261 51.875904,40.296261 50.150905,40.296261 C 48.425906,40.296261 47.025907,38.896261 47.025907,37.171262 C 47.025907,35.446262 48.425906,34.046263 50.150905,34.046263 C 51.875904,34.046263 53.275904,35.446262 53.275904,37.171262 Z M 48.775904,42.671262 C 48.775904,44.396261 47.375904,45.796261 45.650904,45.796261 C 43.925905,45.796261 42.525906,44.396261 42.525906,42.671262 C 42.525906,40.946262 43.925905,39.546263 45.650904,39.546263 C 47.375904,39.546263 48.775904,40.946262 48.775904,42.671262 Z M 57.775904,42.671262 C 57.775904,44.396261 56.375904,45.796261 54.650905,45.796261 C 52.925906,45.796261 51.525907,44.396261 51.525907,42.671262 C 51.525907,40.946262 52.925906,39.546263 54.650905,39.546263 C 56.375904,39.546263 57.775904,40.946262 57.775904,42.671262 Z M 49.236555,41.014512 M 50.712549,41.116305 C 50.763446,50.73572 58.092524,65.953737 58.092524,65.953737 L 53.486402,61.5512 C 52.213993,63.027195 49.949104,65.953737 49.949104,65.953737 C 49.949104,65.953737 48.32042,63.001747 47.149803,61.525752 L 41.907477,65.90284 C 41.907477,65.90284 49.134761,50.837513 49.236555,41.014512 Z" fill="white"/>
							<path d="M 3.275904,-12.828738 C 3.275904,-11.103739 1.875904,-9.703739 0.150905,-9.703739 C -1.574095,-9.703739 -2.974094,-11.103739 -2.974094,-12.828738 C -2.974094,-14.553738 -1.574095,-15.953737 0.150905,-15.953737 C 1.875904,-15.953737 3.275904,-14.553738 3.275904,-12.828738 Z M -1.224097,-7.328738 C -1.224097,-5.603739 -2.624097,-4.203739 -4.349096,-4.203739 C -6.074095,-4.203739 -7.474094,-5.603739 -7.474094,-7.328738 C -7.474094,-9.053738 -6.074095,-10.453737 -4.349096,-10.453737 C -2.624097,-10.453737 -1.224097,-9.053738 -1.224097,-7.328738 Z M 7.775904,-7.328738 C 7.775904,-5.603739 6.375904,-4.203739 4.650905,-4.203739 C 2.925905,-4.203739 1.525907,-5.603739 1.525907,-7.328738 C 1.525907,-9.053738 2.925905,-10.453737 4.650905,-10.453737 C 6.375904,-10.453737 7.775904,-9.053738 7.775904,-7.328738 Z M -0.763446,-8.985488 M 0.712549,-8.883695 C 0.763446,0.73572 8.092523,15.953737 8.092523,15.953737 L 3.486401,11.5512 C 2.213993,13.027195 -0.050896,15.953737 -0.050896,15.953737 C -0.050896,15.953737 -1.679581,13.001747 -2.850197,11.525752 L -8.092523,15.90284 C -8.092523,15.90284 -0.865239,0.837513 -0.763446,-8.985488 Z" fill="white"/>
							<path d="M 103.275904,-12.828738 C 103.275904,-11.103739 101.875904,-9.703739 100.150905,-9.703739 C 98.425906,-9.703739 97.025906,-11.103739 97.025906,-12.828738 C 97.025906,-14.553738 98.425906,-15.953737 100.150905,-15.953737 C 101.875904,-15.953737 103.275904,-14.553738 103.275904,-12.828738 Z M 98.775904,-7.328738 C 98.775904,-5.603739 97.375904,-4.203739 95.650905,-4.203739 C 93.925905,-4.203739 92.525906,-5.603739 92.525906,-7.328738 C 92.525906,-9.053738 93.925905,-10.453737 95.650905,-10.453737 C 97.375904,-10.453737 98.775904,-9.053738 98.775904,-7.328738 Z M 107.775904,-7.328738 C 107.775904,-5.603739 106.375904,-4.203739 104.650905,-4.203739 C 102.925906,-4.203739 101.525907,-5.603739 101.525907,-7.328738 C 101.525907,-9.053738 102.925906,-10.453737 104.650905,-10.453737 C 106.375904,-10.453737 107.775904,-9.053738 107.775904,-7.328738 Z M 99.236555,-8.985488 M 100.712549,-8.883695 C 100.763446,0.73572 108.092524,15.953737 108.092524,15.953737 L 103.486402,11.5512 C 102.213993,13.027195 99.949104,15.953737 99.949104,15.953737 C 99.949104,15.953737 98.32042,13.001747 97.149803,11.525752 L 91.907477,15.90284 C 91.907477,15.90284 99.134762,0.837513 99.236555,-8.985488 Z" fill="white"/>
							<path d="M 3.275904,87.171262 C 3.275904,88.896261 1.875904,90.296261 0.150905,90.296261 C -1.574095,90.296261 -2.974094,88.896261 -2.974094,87.171262 C -2.974094,85.446262 -1.574095,84.046263 0.150905,84.046263 C 1.875904,84.046263 3.275904,85.446262 3.275904,87.171262 Z M -1.224097,92.671262 C -1.224097,94.396261 -2.624097,95.796261 -4.349096,95.796261 C -6.074095,95.796261 -7.474094,94.396261 -7.474094,92.671262 C -7.474094,90.946262 -6.074095,89.546263 -4.349096,89.546263 C -2.624097,89.546263 -1.224097,90.946262 -1.224097,92.671262 Z M 7.775904,92.671262 C 7.775904,94.396261 6.375904,95.796261 4.650905,95.796261 C 2.925905,95.796261 1.525907,94.396261 1.525907,92.671262 C 1.525907,90.946262 2.925905,89.546263 4.650905,89.546263 C 6.375904,89.546263 7.775904,90.946262 7.775904,92.671262 Z M -0.763446,91.014512 M 0.712549,91.116305 C 0.763446,100.73572 8.092523,115.953737 8.092523,115.953737 L 3.486401,111.5512 C 2.213993,113.027195 -0.050896,115.953737 -0.050896,115.953737 C -0.050896,115.953737 -1.679581,113.001747 -2.850197,111.525752 L -8.092523,115.90284 C -8.092523,115.90284 -0.865239,100.837513 -0.763446,91.014512 Z" fill="white"/>
							<path d="M 103.275904,87.171262 C 103.275904,88.896261 101.875904,90.296261 100.150905,90.296261 C 98.425906,90.296261 97.025906,88.896261 97.025906,87.171262 C 97.025906,85.446262 98.425906,84.046263 100.150905,84.046263 C 101.875904,84.046263 103.275904,85.446262 103.275904,87.171262 Z M 98.775904,92.671262 C 98.775904,94.396261 97.375904,95.796261 95.650905,95.796261 C 93.925905,95.796261 92.525906,94.396261 92.525906,92.671262 C 92.525906,90.946262 93.925905,89.546263 95.650905,89.546263 C 97.375904,89.546263 98.775904,90.946262 98.775904,92.671262 Z M 107.775904,92.671262 C 107.775904,94.396261 106.375904,95.796261 104.650905,95.796261 C 102.925906,95.796261 101.525907,94.396261 101.525907,92.671262 C 101.525907,90.946262 102.925906,89.546263 104.650905,89.546263 C 106.375904,89.546263 107.775904,90.946262 107.775904,92.671262 Z M 99.236555,91.014512 M 100.712549,91.116305 C 100.763446,100.73572 108.092524,115.953737 108.092524,115.953737 L 103.486402,111.5512 C 102.213993,113.027195 99.949104,115.953737 99.949104,115.953737 C 99.949104,115.953737 98.32042,113.001747 97.149803,111.525752 L 91.907477,115.90284 C 91.907477,115.90284 99.134762,100.837513 99.236555,91.014512 Z" fill="white"/>
						</pattern>
						<pattern id="ermines-bend" width="100" height="100" patternUnits="userSpaceOnUse" patternTransform="scale(0.25)">
							<rect width="100" height="100" fill="black" />
							<g transform-origin="50 50" >
								<path d="M 53.275904,37.171262 C 53.275904,38.896261 51.875904,40.296261 50.150905,40.296261 C 48.425906,40.296261 47.025907,38.896261 47.025907,37.171262 C 47.025907,35.446262 48.425906,34.046263 50.150905,34.046263 C 51.875904,34.046263 53.275904,35.446262 53.275904,37.171262 Z M 48.775904,42.671262 C 48.775904,44.396261 47.375904,45.796261 45.650904,45.796261 C 43.925905,45.796261 42.525906,44.396261 42.525906,42.671262 C 42.525906,40.946262 43.925905,39.546263 45.650904,39.546263 C 47.375904,39.546263 48.775904,40.946262 48.775904,42.671262 Z M 57.775904,42.671262 C 57.775904,44.396261 56.375904,45.796261 54.650905,45.796261 C 52.925906,45.796261 51.525907,44.396261 51.525907,42.671262 C 51.525907,40.946262 52.925906,39.546263 54.650905,39.546263 C 56.375904,39.546263 57.775904,40.946262 57.775904,42.671262 Z M 49.236555,41.014512 M 50.712549,41.116305 C 50.763446,50.73572 58.092524,65.953737 58.092524,65.953737 L 53.486402,61.5512 C 52.213993,63.027195 49.949104,65.953737 49.949104,65.953737 C 49.949104,65.953737 48.32042,63.001747 47.149803,61.525752 L 41.907477,65.90284 C 41.907477,65.90284 49.134761,50.837513 49.236555,41.014512 Z" fill="white" transform-origin="50 50" transform="rotate(-45)" />
								<path d="M 3.275904,-12.828738 C 3.275904,-11.103739 1.875904,-9.703739 0.150905,-9.703739 C -1.574095,-9.703739 -2.974094,-11.103739 -2.974094,-12.828738 C -2.974094,-14.553738 -1.574095,-15.953737 0.150905,-15.953737 C 1.875904,-15.953737 3.275904,-14.553738 3.275904,-12.828738 Z M -1.224097,-7.328738 C -1.224097,-5.603739 -2.624097,-4.203739 -4.349096,-4.203739 C -6.074095,-4.203739 -7.474094,-5.603739 -7.474094,-7.328738 C -7.474094,-9.053738 -6.074095,-10.453737 -4.349096,-10.453737 C -2.624097,-10.453737 -1.224097,-9.053738 -1.224097,-7.328738 Z M 7.775904,-7.328738 C 7.775904,-5.603739 6.375904,-4.203739 4.650905,-4.203739 C 2.925905,-4.203739 1.525907,-5.603739 1.525907,-7.328738 C 1.525907,-9.053738 2.925905,-10.453737 4.650905,-10.453737 C 6.375904,-10.453737 7.775904,-9.053738 7.775904,-7.328738 Z M -0.763446,-8.985488 M 0.712549,-8.883695 C 0.763446,0.73572 8.092523,15.953737 8.092523,15.953737 L 3.486401,11.5512 C 2.213993,13.027195 -0.050896,15.953737 -0.050896,15.953737 C -0.050896,15.953737 -1.679581,13.001747 -2.850197,11.525752 L -8.092523,15.90284 C -8.092523,15.90284 -0.865239,0.837513 -0.763446,-8.985488 Z" fill="white" transform-origin="0 0" transform="rotate(-45)" />
								<path d="M 103.275904,-12.828738 C 103.275904,-11.103739 101.875904,-9.703739 100.150905,-9.703739 C 98.425906,-9.703739 97.025906,-11.103739 97.025906,-12.828738 C 97.025906,-14.553738 98.425906,-15.953737 100.150905,-15.953737 C 101.875904,-15.953737 103.275904,-14.553738 103.275904,-12.828738 Z M 98.775904,-7.328738 C 98.775904,-5.603739 97.375904,-4.203739 95.650905,-4.203739 C 93.925905,-4.203739 92.525906,-5.603739 92.525906,-7.328738 C 92.525906,-9.053738 93.925905,-10.453737 95.650905,-10.453737 C 97.375904,-10.453737 98.775904,-9.053738 98.775904,-7.328738 Z M 107.775904,-7.328738 C 107.775904,-5.603739 106.375904,-4.203739 104.650905,-4.203739 C 102.925906,-4.203739 101.525907,-5.603739 101.525907,-7.328738 C 101.525907,-9.053738 102.925906,-10.453737 104.650905,-10.453737 C 106.375904,-10.453737 107.775904,-9.053738 107.775904,-7.328738 Z M 99.236555,-8.985488 M 100.712549,-8.883695 C 100.763446,0.73572 108.092524,15.953737 108.092524,15.953737 L 103.486402,11.5512 C 102.213993,13.027195 99.949104,15.953737 99.949104,15.953737 C 99.949104,15.953737 98.32042,13.001747 97.149803,11.525752 L 91.907477,15.90284 C 91.907477,15.90284 99.134762,0.837513 99.236555,-8.985488 Z" fill="white" transform-origin="100 0" transform="rotate(-45)" />
								<path d="M 3.275904,87.171262 C 3.275904,88.896261 1.875904,90.296261 0.150905,90.296261 C -1.574095,90.296261 -2.974094,88.896261 -2.974094,87.171262 C -2.974094,85.446262 -1.574095,84.046263 0.150905,84.046263 C 1.875904,84.046263 3.275904,85.446262 3.275904,87.171262 Z M -1.224097,92.671262 C -1.224097,94.396261 -2.624097,95.796261 -4.349096,95.796261 C -6.074095,95.796261 -7.474094,94.396261 -7.474094,92.671262 C -7.474094,90.946262 -6.074095,89.546263 -4.349096,89.546263 C -2.624097,89.546263 -1.224097,90.946262 -1.224097,92.671262 Z M 7.775904,92.671262 C 7.775904,94.396261 6.375904,95.796261 4.650905,95.796261 C 2.925905,95.796261 1.525907,94.396261 1.525907,92.671262 C 1.525907,90.946262 2.925905,89.546263 4.650905,89.546263 C 6.375904,89.546263 7.775904,90.946262 7.775904,92.671262 Z M -0.763446,91.014512 M 0.712549,91.116305 C 0.763446,100.73572 8.092523,115.953737 8.092523,115.953737 L 3.486401,111.5512 C 2.213993,113.027195 -0.050896,115.953737 -0.050896,115.953737 C -0.050896,115.953737 -1.679581,113.001747 -2.850197,111.525752 L -8.092523,115.90284 C -8.092523,115.90284 -0.865239,100.837513 -0.763446,91.014512 Z" fill="white" transform-origin="0 100" transform="rotate(-45)" />
								<path d="M 103.275904,87.171262 C 103.275904,88.896261 101.875904,90.296261 100.150905,90.296261 C 98.425906,90.296261 97.025906,88.896261 97.025906,87.171262 C 97.025906,85.446262 98.425906,84.046263 100.150905,84.046263 C 101.875904,84.046263 103.275904,85.446262 103.275904,87.171262 Z M 98.775904,92.671262 C 98.775904,94.396261 97.375904,95.796261 95.650905,95.796261 C 93.925905,95.796261 92.525906,94.396261 92.525906,92.671262 C 92.525906,90.946262 93.925905,89.546263 95.650905,89.546263 C 97.375904,89.546263 98.775904,90.946262 98.775904,92.671262 Z M 107.775904,92.671262 C 107.775904,94.396261 106.375904,95.796261 104.650905,95.796261 C 102.925906,95.796261 101.525907,94.396261 101.525907,92.671262 C 101.525907,90.946262 102.925906,89.546263 104.650905,89.546263 C 106.375904,89.546263 107.775904,90.946262 107.775904,92.671262 Z M 99.236555,91.014512 M 100.712549,91.116305 C 100.763446,100.73572 108.092524,115.953737 108.092524,115.953737 L 103.486402,111.5512 C 102.213993,113.027195 99.949104,115.953737 99.949104,115.953737 C 99.949104,115.953737 98.32042,113.001747 97.149803,111.525752 L 91.907477,115.90284 C 91.907477,115.90284 99.134762,100.837513 99.236555,91.014512 Z" fill="white" transform-origin="100 100" transform="rotate(-45)" />
							</g>
						</pattern>
						<pattern id="ermines-sinister" width="100" height="100" patternUnits="userSpaceOnUse" patternTransform="scale(0.25)">
							<rect width="100" height="100" fill="black" />
							<g transform-origin="50 50" transform="rotate(45)">
								<path d="M 53.275904,37.171262 C 53.275904,38.896261 51.875904,40.296261 50.150905,40.296261 C 48.425906,40.296261 47.025907,38.896261 47.025907,37.171262 C 47.025907,35.446262 48.425906,34.046263 50.150905,34.046263 C 51.875904,34.046263 53.275904,35.446262 53.275904,37.171262 Z M 48.775904,42.671262 C 48.775904,44.396261 47.375904,45.796261 45.650904,45.796261 C 43.925905,45.796261 42.525906,44.396261 42.525906,42.671262 C 42.525906,40.946262 43.925905,39.546263 45.650904,39.546263 C 47.375904,39.546263 48.775904,40.946262 48.775904,42.671262 Z M 57.775904,42.671262 C 57.775904,44.396261 56.375904,45.796261 54.650905,45.796261 C 52.925906,45.796261 51.525907,44.396261 51.525907,42.671262 C 51.525907,40.946262 52.925906,39.546263 54.650905,39.546263 C 56.375904,39.546263 57.775904,40.946262 57.775904,42.671262 Z M 49.236555,41.014512 M 50.712549,41.116305 C 50.763446,50.73572 58.092524,65.953737 58.092524,65.953737 L 53.486402,61.5512 C 52.213993,63.027195 49.949104,65.953737 49.949104,65.953737 C 49.949104,65.953737 48.32042,63.001747 47.149803,61.525752 L 41.907477,65.90284 C 41.907477,65.90284 49.134761,50.837513 49.236555,41.014512 Z" fill="white" />
								<path d="M 3.275904,-12.828738 C 3.275904,-11.103739 1.875904,-9.703739 0.150905,-9.703739 C -1.574095,-9.703739 -2.974094,-11.103739 -2.974094,-12.828738 C -2.974094,-14.553738 -1.574095,-15.953737 0.150905,-15.953737 C 1.875904,-15.953737 3.275904,-14.553738 3.275904,-12.828738 Z M -1.224097,-7.328738 C -1.224097,-5.603739 -2.624097,-4.203739 -4.349096,-4.203739 C -6.074095,-4.203739 -7.474094,-5.603739 -7.474094,-7.328738 C -7.474094,-9.053738 -6.074095,-10.453737 -4.349096,-10.453737 C -2.624097,-10.453737 -1.224097,-9.053738 -1.224097,-7.328738 Z M 7.775904,-7.328738 C 7.775904,-5.603739 6.375904,-4.203739 4.650905,-4.203739 C 2.925905,-4.203739 1.525907,-5.603739 1.525907,-7.328738 C 1.525907,-9.053738 2.925905,-10.453737 4.650905,-10.453737 C 6.375904,-10.453737 7.775904,-9.053738 7.775904,-7.328738 Z M -0.763446,-8.985488 M 0.712549,-8.883695 C 0.763446,0.73572 8.092523,15.953737 8.092523,15.953737 L 3.486401,11.5512 C 2.213993,13.027195 -0.050896,15.953737 -0.050896,15.953737 C -0.050896,15.953737 -1.679581,13.001747 -2.850197,11.525752 L -8.092523,15.90284 C -8.092523,15.90284 -0.865239,0.837513 -0.763446,-8.985488 Z" fill="white" />
								<path d="M 103.275904,-12.828738 C 103.275904,-11.103739 101.875904,-9.703739 100.150905,-9.703739 C 98.425906,-9.703739 97.025906,-11.103739 97.025906,-12.828738 C 97.025906,-14.553738 98.425906,-15.953737 100.150905,-15.953737 C 101.875904,-15.953737 103.275904,-14.553738 103.275904,-12.828738 Z M 98.775904,-7.328738 C 98.775904,-5.603739 97.375904,-4.203739 95.650905,-4.203739 C 93.925905,-4.203739 92.525906,-5.603739 92.525906,-7.328738 C 92.525906,-9.053738 93.925905,-10.453737 95.650905,-10.453737 C 97.375904,-10.453737 98.775904,-9.053738 98.775904,-7.328738 Z M 107.775904,-7.328738 C 107.775904,-5.603739 106.375904,-4.203739 104.650905,-4.203739 C 102.925906,-4.203739 101.525907,-5.603739 101.525907,-7.328738 C 101.525907,-9.053738 102.925906,-10.453737 104.650905,-10.453737 C 106.375904,-10.453737 107.775904,-9.053738 107.775904,-7.328738 Z M 99.236555,-8.985488 M 100.712549,-8.883695 C 100.763446,0.73572 108.092524,15.953737 108.092524,15.953737 L 103.486402,11.5512 C 102.213993,13.027195 99.949104,15.953737 99.949104,15.953737 C 99.949104,15.953737 98.32042,13.001747 97.149803,11.525752 L 91.907477,15.90284 C 91.907477,15.90284 99.134762,0.837513 99.236555,-8.985488 Z" fill="white" />
								<path d="M 3.275904,87.171262 C 3.275904,88.896261 1.875904,90.296261 0.150905,90.296261 C -1.574095,90.296261 -2.974094,88.896261 -2.974094,87.171262 C -2.974094,85.446262 -1.574095,84.046263 0.150905,84.046263 C 1.875904,84.046263 3.275904,85.446262 3.275904,87.171262 Z M -1.224097,92.671262 C -1.224097,94.396261 -2.624097,95.796261 -4.349096,95.796261 C -6.074095,95.796261 -7.474094,94.396261 -7.474094,92.671262 C -7.474094,90.946262 -6.074095,89.546263 -4.349096,89.546263 C -2.624097,89.546263 -1.224097,90.946262 -1.224097,92.671262 Z M 7.775904,92.671262 C 7.775904,94.396261 6.375904,95.796261 4.650905,95.796261 C 2.925905,95.796261 1.525907,94.396261 1.525907,92.671262 C 1.525907,90.946262 2.925905,89.546263 4.650905,89.546263 C 6.375904,89.546263 7.775904,90.946262 7.775904,92.671262 Z M -0.763446,91.014512 M 0.712549,91.116305 C 0.763446,100.73572 8.092523,115.953737 8.092523,115.953737 L 3.486401,111.5512 C 2.213993,113.027195 -0.050896,115.953737 -0.050896,115.953737 C -0.050896,115.953737 -1.679581,113.001747 -2.850197,111.525752 L -8.092523,115.90284 C -8.092523,115.90284 -0.865239,100.837513 -0.763446,91.014512 Z" fill="white" />
								<path d="M 103.275904,87.171262 C 103.275904,88.896261 101.875904,90.296261 100.150905,90.296261 C 98.425906,90.296261 97.025906,88.896261 97.025906,87.171262 C 97.025906,85.446262 98.425906,84.046263 100.150905,84.046263 C 101.875904,84.046263 103.275904,85.446262 103.275904,87.171262 Z M 98.775904,92.671262 C 98.775904,94.396261 97.375904,95.796261 95.650905,95.796261 C 93.925905,95.796261 92.525906,94.396261 92.525906,92.671262 C 92.525906,90.946262 93.925905,89.546263 95.650905,89.546263 C 97.375904,89.546263 98.775904,90.946262 98.775904,92.671262 Z M 107.775904,92.671262 C 107.775904,94.396261 106.375904,95.796261 104.650905,95.796261 C 102.925906,95.796261 101.525907,94.396261 101.525907,92.671262 C 101.525907,90.946262 102.925906,89.546263 104.650905,89.546263 C 106.375904,89.546263 107.775904,90.946262 107.775904,92.671262 Z M 99.236555,91.014512 M 100.712549,91.116305 C 100.763446,100.73572 108.092524,115.953737 108.092524,115.953737 L 103.486402,111.5512 C 102.213993,113.027195 99.949104,115.953737 99.949104,115.953737 C 99.949104,115.953737 98.32042,113.001747 97.149803,111.525752 L 91.907477,115.90284 C 91.907477,115.90284 99.134762,100.837513 99.236555,91.014512 Z" fill="white" />
							</g>
						</pattern>
						<pattern id="chequeredBG" width="100" height="100" patternUnits="userSpaceOnUse" patternTransform="scale(0.125)">
							<rect x="0" y="0" width="100" height="100" fill="white"/>
							<rect x="0" y="0" width="50" height="50" fill="rgb(200,200,200)"/>
							<rect x="50" y="50" width="50" height="50" fill="rgb(200,200,200)"/>
						</pattern>
					</defs>
					<rect x="0" y="0" width="100%" height="100%" fill="url(#chequeredBG)" />
					<g id="escutcheon">
						<!-- alternative shield outline (my creation) -->
						<!--path
							d="M 20 5 L 180 5 C 180 5 182.113598 84.825528 167.003707 137.362438 C 151.893806 189.899348 102.105477 195.000018 100 195 C 97.894524 195 48.106195 189.899348 32.996295 137.362438 C 17.886404 84.825528 20 5 20 5 Z"
							id="shield"
							class="heraldry-ermine" />
						<path
							d="M 20 5 L 180 5 C 180 5 182.113598 84.825528 167.003707 137.362438 C 151.893806 189.899348 102.105477 195.000018 100 195 C 97.894524 195 48.106195 189.899348 32.996295 137.362438 C 17.886404 84.825528 20 5 20 5 Z"
							id="shieldOutline"
							stroke-width="1"
							stroke="black"
							stroke-linecap="square"
							stroke-linejoin="bevel"
							fill="none" /-->
						<!-- shield outline (adapted from Wikimedia Commons) -->
						<path
							d="M 20 12.5 L 179.999996 12.5 L 179.999996 81.366166 C 179.999996 157.611827 99.999998 187.500003 99.999998 187.500003 C 99.999998 187.500003 20 157.611827 20 81.366166 Z"
							id="shield"
							class="heraldry-ermine" />
						<path
							d="M 20 12.5 L 179.999996 12.5 L 179.999996 81.366166 C 179.999996 157.611827 99.999998 187.500003 99.999998 187.500003 C 99.999998 187.500003 20 157.611827 20 81.366166 Z"
							id="shieldOutline"
							stroke-width="1"
							stroke="black"
							stroke-linecap="square"
							stroke-linejoin="bevel"
							fill="none" />
					</g>
				</svg>
                <div id="shieldCover" class="loadingScreen">
                    <h1 id="pleaseHeading">Please</h1>
                    <h2 id="waitHeading">Wait...</h2>
                </div>
			</div>
            <div id="bottomHalf">
                <div id="wrapper">
                    <div id="left">
                        <textarea id="blazonText" disabled></textarea>
                    </div>
                    <div id="middle"></div>
                    <div id="right">
                        <div id="shapeSelect">
                            <input type="radio" name="shape" value="B" onchange="changeShield(shieldB)" checked>Noble<br>
                            <input type="radio" name="shape" value="A" onchange="changeShield(ellipsePath)">Spiritual<br>
                        </div>
                        <div id="styleSelect">
                            <input type="radio" name="style" value="normal" onchange="changeHeraldryCSS('heraldry.css')" checked> Full colour<br>
                            <input type="radio" name="style" value="bw" onchange="changeHeraldryCSS('heraldry-bw.css')"> Line art<br>
                        </div>
                    </div>
                </div>
                <div id="buttonContainer">
                    <button id="blazonButton" type="submit" onclick="drawUserBlazon()" disabled>Emblazon Arms</button>
                </div>
                <div id="syntax" style="display:none">
                    <h1>Syntax Tree</h1>
                    <div class="console" id="displayPara">
                        root<br>
                        -node1<br>
                        -node2<br>
                        --subnode1<br>
                        -node3<br>
                    </div>
                </div>
            </div>
		</div>
	</body>
</html>
