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
                            <p class="menu-item expanding-button" id="exampleBlazons">Example blazons</p>
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
<?php require "startingsvg.php"; ?>
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
