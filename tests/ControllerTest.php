<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use HeraldryEngine\Mvc\Model;
use HeraldryEngine\Mvc\Controller;
use HeraldryEngine\Http\Request;
use HeraldryEngine\Http\SessionContainer;
use HeraldryEngine\Http\ServerContainer;
use HeraldryEngine\Http\CookieContainer;

/**
 * @testdox Generic controller class
 */
final class ControllerTestCase extends TestCase{
		public function testCheckingPrivilegesGivesCorrectResult(){
			$model = new Model(
				new Request(
					new CookieContainer([]),
					new ServerContainer([]),
					new SessionContainer(['accessLevel'=>1])
					)
				);
			$controller=new Controller($model);
			$this->assertEquals(
				false,
				$controller->checkPrivNotLess(0)
				);
			$this->assertEquals(
				true,
				$controller->checkPrivNotLess(1)
				);
			$this->assertEquals(
				true,
				$controller->checkPrivNotLess(2)
				);
		}
		public function testCheckingPrivilegesWhenPrivilegesNotSetAlwaysFails(){
			$model = new Model(
				new Request(
					new CookieContainer([]),
					new ServerContainer([]),
					new SessionContainer([])
					)
				);
			$controller=new Controller($model);
			$this->assertEquals(
				false,
				$controller->checkPrivNotLess(0)
				);
			$this->assertEquals(
				false,
				$controller->checkPrivNotLess(1)
				);
			$this->assertEquals(
				false,
				$controller->checkPrivNotLess(2)
				);
			$this->assertEquals(
				false,
				$controller->checkPrivNotLess(null)
				);
			$this->assertEquals(
				false,
				$controller->checkPrivNotLess('banana')
				);
		}
}