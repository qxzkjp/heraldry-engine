<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use HeraldryEngine\Mvc\Model;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\Http\Request;
use HeraldryEngine\Http\SessionContainer;
use HeraldryEngine\Http\ServerContainer;
use HeraldryEngine\Http\CookieContainer;

/**
 * @testdox Generic view class
 */
final class ViewTestCase extends TestCase{
		public function testSettingParameterWorksAsExpected(){
			$model = new Model(
				new Request(
					new CookieContainer([]),
					new ServerContainer([]),
					new SessionContainer([])
					)
				);
			$view=new View(null,$model);
			$view->setParam('hello','world');
			$this->assertEquals(
				'world',
				$view->getParam('hello')
				);
			$view->setParam('hello','everyone');
			$this->assertEquals(
				'everyone',
				$view->getParam('hello')
				);
		}
		public function testAppendingToUnsetParameterWorksAsExpected(){
			$model = new Model(
				new Request(
					new CookieContainer([]),
					new ServerContainer([]),
					new SessionContainer([])
					)
				);
			$view=new View(null,$model);
			$view->appendParam('hello','world');
			$this->assertEquals(
				['world'],
				$view->getParam('hello')
				);
		}
		public function testAppendingToSetParameterWorksAsExpected(){
			$model = new Model(
				new Request(
					new CookieContainer([]),
					new ServerContainer([]),
					new SessionContainer([])
					)
				);
			$view=new View(null,$model);
			$view->setParam('newParam',['hello']);
			$view->appendParam('newParam','world');
			$this->assertEquals(
				['hello','world'],
				$view->getParam('newParam')
				);
		}
		public function testAppendingToParameterWhichIsNotAnArrayWorksAsExpected(){
			$model = new Model(
				new Request(
					new CookieContainer([]),
					new ServerContainer([]),
					new SessionContainer([])
					)
				);
			$view=new View(null,$model);
			$view->setParam('newParam','hello');
			$view->appendParam('newParam','world');
			$this->assertEquals(
				['hello','world'],
				$view->getParam('newParam')
				);
		}
}