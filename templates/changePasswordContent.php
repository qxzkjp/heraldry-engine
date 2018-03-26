			<div id="bottomHalf">
				<form action="<?=$this->params['pageName']?>" method="post">
				<p>
<?php if(array_key_exists("changeID", $this->params)): ?>
				Changing password for user ID <?php echo $this->params["changeID"] ?></p>
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
<?php if(array_key_exists("changeID", $this->params)):?>
					<input type="hidden" name="ID" value="<?=$this->params["changeID"] ?>"></input>
<?php endif ?>
				</form>
			</div>