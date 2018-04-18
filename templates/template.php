<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
		<title>Heraldry Engine</title>
		<link rel="icon" type="image/x-icon" href="logo.png" />
		<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<link href='styles/style.css' rel='stylesheet' type='text/css'>
		<?php if(array_key_exists("nightMode", $this->params)): ?>
		<link href='styles/night-mode.css' id="day-css" rel='stylesheet' type='text/css'>
		<?php else: ?>
		<link href='styles/day-mode.css' id="day-css" rel='stylesheet' type='text/css'>
		<?php endif ?>
		<?php foreach($this->params["cssList"] as $item):?>
		<link
			<?php if(array_key_exists("id",$item)):?>
			id="<?=$item["id"]?>"
			<?php endif ?>
			href="styles/<?=$item["name"]?>.css" rel="stylesheet" type="text/css">
		<?php endforeach?>

		<?php foreach($this->params["scriptList"] as $name):?>
		<script type="text/javascript" src="scripts/<?=$name?>.js"></script>
		<?php endforeach?>

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
							<?php foreach($this->params["menuList"] as $item): 
									$class="menu-item";
									if(array_key_exists("expandable",$item)){
										$class=$class." expanding-button";
									}
									if(array_key_exists("toggle",$item)){
										$class=$class." toggle-button";
									}
							?>

							<a href="<?=$item["href"]?>"
								class="<?=$class?>"
								<?php if(array_key_exists("id",$item)){
									echo "id='".$item["id"]."'";
									}?>
								<?php if(array_key_exists("onclick",$item)){
									echo 'onclick="'.$item["onclick"].'"';
								}?> >
								<?=$item["label"]?></a>
								<?php if(array_key_exists("expandable",$item)){
										require $item["expandable"];
								}?>
							<?php endforeach ?>

							<a href="#" class="menu-item toggle-button" id="toggleNight">
								Night mode</a>
							
							<?php if(array_key_exists("loggedIn",$this->params)):?>

							<a href="logout" class="menu-item">Log out</a>
							<?php else:?>

							<a href="login" class="menu-item">Log in</a>
							<?php endif?>

						</div>
                        <div id="versionContainer">Heraldry Engine<br />Version 0.1 (Alpha)<br />By Deus Kane</div>
                    </div>
                </div>
            </div>
		</div>
		<div id="content">
			<hgroup id="mainHead">
				<?php if(isset($this->params["primaryHead"])):?>
				<h1 id="heraldryHead"><?=$this->params["primaryHead"]?>&nbsp;</h1>
				<?php endif?>

				<?php if(isset($this->params["secondaryHead"])):?>
				<h1 id="engineHead"><?=$this->params["secondaryHead"]?></h1>
				<?php endif?>

			</hgroup>
			<div id="messageContainer">
<?php if( array_key_exists('errorMessage', $this->params)):?>
				<p class="error"><?=$this->params['errorMessage']?></p>
<?php endif?>
<?php if(array_key_exists('successMessage',$this->params)):?>
				<p><?=$this->params['successMessage']?></p>
<?php endif?>
<?php if(array_key_exists("debug", $this->params)
			&& array_key_exists('debugMessage', $this->params)):?>
				<p><?=$this->params['debugMessage']?></p>
<?php endif?>
			</div>
<?php require $this->params["content"]; ?>
		</div>
	</body>
</html>