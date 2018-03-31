<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use HeraldryEngine\AdminPanel\AdminPanelModel;
use HeraldryEngine\Http\Request;
use HeraldryEngine\Http\SessionContainer;
use HeraldryEngine\Http\ServerContainer;
use HeraldryEngine\Http\CookieContainer;
use HeraldryEngine\SessionHandler;

/**
 * @testdox AdminPanel (database-containing) model class
 */
final class AdminPanelModelTestCase extends TestCase{
		public function testPreparingAStatementWorksAsExpected(){
			$handler = new SessionHandler();
			$config = [
				'db.host'=>'127.0.0.1',
				'db.user'=>'root',
				'db.pass'=>'',
				'db.name'=>'test'];
			$request = new Request(
							new CookieContainer([]),
							new ServerContainer([]),
							new SessionContainer([])
							);
			$model = new AdminPanelModel(
				$config,
				$handler,
				$request
				);
			$this->assertEquals(
						'mysqli_stmt',
						get_class($model->prepareStatement('SELECT * FROM users'))
						);
			$this->assertEquals(
						'',
						$model->lastDatabaseError()
						);
		}
}
