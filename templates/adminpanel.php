		<div id="bottomHalf" style="font-family:serif">
			<?php if($this->params['errorMessage']!=""):?>
				<p class="error"><?=$this->params['errorMessage']?></p>
			<?php endif?>
			<?php if($this->params['successMessage']!=""):?>
				<p><?=$this->params['successMessage']?></p>
			<?php endif?>
			<h3>Users</h3>
				<table>
					<tr>
						<th>ID</th>
						<th>username</th>
						<th>status</th>
					</tr>
					<?php
						foreach($this->params['userRows'] as $row) {
							$status;
								if((int)$row['accessLevel'] == 0)
									$status="admin";
								elseif ((int)$row['accessLevel'] == 1)
									$status="standard";
								elseif ((int)$row['accessLevel'] == 2)
									$status="disabled";
								else
									$status="unknown";
						?>
							<tr>
								<td><?=$row['ID']?></td>
								<td><?=$row['userName']?></td>
								<td><?=$status?></td>
								<td><a href="#"
									onclick="post('changepassword.php',{'ID': '<?=$row['ID']?>'})"
									>Change password</a>
								</td>
								<td><a href="#"
									onclick="post('<?=$this->params["pageName"]?>',{'action' : 'deleteUser', 'ID': '<?=$row['ID']?>'})"
									>Delete user</a>
								</td>
								<td><?php
									if($row["accessLevel"]!=0): 
									?><a href="#"
										onclick="post('<?=$this->params["pageName"]?>',{'action' : 'promoteUser', 'ID': '<?=$row['ID']?>'})"
										>Promote user</a>
								<?php else: ?>
										Promote user
								<?php endif ?></td>
								<td>
<?php if($row["accessLevel"]<1): ?>
									<a href="#"
										onclick="post('<?=$this->params["pageName"]?>',{'action' : 'demoteUser', 'ID': '<?=$row['ID']?>'})"
										>Demote user</a>
<?php elseif($row["accessLevel"]>1): ?>
									<a href="#"
										onclick="post('<?=$this->params["pageName"]?>',{'action' : 'demoteUser', 'ID': '<?=$row['ID']?>'})"
										>Enable user</a>
<?php else: ?>
									Demote user
<?php endif ?>
								</td>
								<td><?php
									if($row["accessLevel"]!=2):
									?><a href="#"
										onclick="post('<?=$this->params["pageName"]?>',{'action' : 'disableUser', 'ID': '<?=$row['ID']?>'})"
										>Disable user</a>
								<?php else: ?>
										Disable user
								<?php endif ?></td>
							</tr>
					<?php
						}
					?>
				</table>
				<h3>Authenticated sessions</h3>
				<table>
					<tr>
						<th>Session ID</th>
						<th>User name</th>
						<th>User IP</th>
						<th>Location</th>
						<th>Time created</th>
						<th>Time expires</th>
						<th>Browser</th>
						<th>OS</th>
						<th></th>
					</tr>
					<?php
					foreach($this->params['sessionList'] as $id => $data){
						if(is_array($data) && array_key_exists("userID",$data)){
							if(!array_key_exists("expiry",$data)
								|| $data["expiry"] >= time()){
					?>
					<tr>
						<td><?=$id?></td>
						<td><?php
						if(array_key_exists("userID",$data)){
							echo $this->params['users'][$data["userID"]];
						}
						?></td>
						<td><?php
							if(array_key_exists("userIP",$data)){
								echo $data["userIP"];
							}else{
								echo "unknown";
							}
							echo "<!--".$data["geoIP"]."-->";
						?></td>
						<td><?php
						if(array_key_exists("city",$data)){
							echo $data["city"].", ";
						}
						if(array_key_exists("countryName",$data)){
							echo str_replace(" ","&nbsp;",
								$data["countryName"]);
						}else{
							echo "unknown";
						}
						?></td>
						<td><?php
							if(array_key_exists("startTime",$data)){
								date_default_timezone_set('Europe/London');
								echo date('d/m/Y H:i:s', $data["startTime"]);
							}
						?></td>
						<td><?php
							if(array_key_exists("expiry",$data)){
								date_default_timezone_set('Europe/London');
								echo date('d/m/Y H:i:s', $data["expiry"]);
							}else{
								echo "Never";
							}
						?></td>
						<td><?php
							if(array_key_exists("browser",$data)){
								echo $data["browser"];
							}else{
								echo "unknown";
							}
						?></td>
						<td><?php
							if(array_key_exists("OS",$data)){
								echo str_replace(" ", "&nbsp;", $data["OS"]);
							}else{
								echo "unknown";
							}
						?></td>
						<td><?php if($id!=session_id()):?><a href="#"
							onclick="post('<?=$this->params["pageName"]?>',{'action' : 'deleteSession', 'ID': '<?=$id?>'})">
							Delete session</a><?php endif ?>
					</tr>
					<?php
							}
						}
					}
				?></table>
				<p>
					<a href="#"
						onclick="post('<?=$this->params["pageName"]?>',{'action' : 'garbageCollect'})"
						>Collect garbage</a>
				</p>
			</div>
		</div>