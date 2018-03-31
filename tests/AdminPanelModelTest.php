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
				'db.host'=>'localhost',
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
			$stmt = $model->prepareStatement('SELECT * FROM users');
			$this->assertEquals(
						'mysqli_stmt',
						get_class($stmt)
						);
			$stmt->close();
		}
		public function testTrimLogsActuallyDeletesLogs(){
			$handler = new SessionHandler();
			$config = [
				'db.host'=>'localhost',
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
			$stmt = $model->prepareStatement('DELETE FROM failureLogs WHERE TRUE');
			$stmt->execute();
			$stmt->close();
			//One big insert statement is a hell of a lot faster than 100 little ones
			$query='INSERT INTO failureLogs (userName, accessTime, IP, isIPv6) VALUES ';
			$innerRep='("admin", NOW(), 1, TRUE)';
			for($i=0;$i<50;++$i){
				$query.=$innerRep.', ';
			}
			$innerRep='("admin", (NOW() - INTERVAL 14 DAY), UNHEX(01), TRUE)';
			for($i=0;$i<49;++$i){
				$query.=$innerRep.', ';
			}
			$query.=$innerRep;
			$stmt = $model->prepareStatement($query);
			$stmt->execute();
			$stmt->close();
			$model->trimLogs();
			$stmt = $model->prepareStatement('SELECT COUNT(*) FROM failurelogs WHERE TRUE');
			$stmt->execute();
			$result = $stmt->get_result();
			$this->assertEquals(
						'',
						$model->lastDatabaseError()
						);
			$row = $result->fetch_row();
			$stmt->close();
			$this->assertEquals(
				50,
				$row[0]
			);
		}
}
