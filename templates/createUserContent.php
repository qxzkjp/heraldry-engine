			<div id="bottomHalf">
<?php if($this->params['errorMessage']!=""):?>
				<p class="error"><?=$this->params['errorMessage']?></p>
<?php endif?>
<?php if($this->params['successMessage']!=""):?>
				<p><?=$this->params['successMessage']?></p>
<?php endif?>
<?php if(array_key_exists("debug", $this->params)
			&& $this->params['debugMessage']!=""):?>
				<p><?=$this->params['debugMessage']?></p>
<?php endif?>

				<form action="<?=$this->params['pageName']?>" method="post">
					<table>
						<tr>
							<td>User name</td>
							<td><input type="text" name="newUser"></td>
						</tr>
						<tr>
							<td>Password</td>
							<td><input type="password" name="newPassword"></td>
						</tr>
						<tr>
							<td>Confirm password</td>
							<td><input type="password" name="checkPassword"></td>
						</tr>
						<tr>
							<td>Create as admin</td>
							<td><input type="checkbox" name="asAdmin" value="true"></td>
						</tr>
					</table>
					<input type="submit" name="submit" value="Create user"/>
				</form>
			</div>