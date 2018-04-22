			<div id="bottomHalf">

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
                    <input type="hidden" name="CSRF" value="<?=$this->params['CSRF']?>">
				</form>
			</div>