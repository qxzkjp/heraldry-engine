<div style="display:none" id="exampleContainer">
<?php foreach($this->params["demoBlazons"] as $item): ?>
	<div class="demoBlazon"><?php
	if(array_key_exists("label",$item)){
		echo $item["label"];
		echo '<span class="blazonText">';
		echo $item["blazon"];
		echo '</span>';
	}else{
		echo $item["blazon"];
	}
?>
</div>
<?php endforeach ?>
</div>