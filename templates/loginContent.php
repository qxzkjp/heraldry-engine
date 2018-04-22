			<div id="bottomHalf">
				<form method="post" action="<?=$this->params['pageName'] ?>">
					<input type="text" name="username"></input><br/>
					<input type="password" name="password"></input>
					<input type="submit" name="submit" value="Log in"/>
                    <input type="hidden" name="CSRF" value="<?=$this->params['CSRF']?>">
				</form>
			</div>