<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use HeraldryEngine\Mvc\Model;
use HeraldryEngine\Http\Request;
use HeraldryEngine\Http\SessionContainer;
use HeraldryEngine\Http\ServerContainer;
use HeraldryEngine\Http\CookieContainer;

/**
 * @testdox Generic model class
 */
final class ModelTestCase extends TestCase{
	public function testGetingAnUnsetCookieReturnsAnEmptyString(){
		$model = new Model(
			new Request(
				new CookieContainer([]),
				new ServerContainer([]),
				new SessionContainer([])
			)
		);
		$this->assertEquals(
			'',
			$model->getCookies()->getCookie('nonexistent')
		);
	}
	public function testGetingASetCookieReturnsTheCorrectValue(){
		$model = new Model(
			new Request(
				new CookieContainer(['cookie'=>'value']),
				new ServerContainer([]),
				new SessionContainer([])
			)
		);
		$this->assertEquals(
			'value',
			$model->getCookies()->getCookie('cookie')
		);
	}
	public function testGetingAnUnsetSessionVariableReturnsNull(){
		$model = new Model(
			new Request(
				new CookieContainer([]),
				new ServerContainer([]),
				new SessionContainer([])
			)
		);
		$this->assertNull(
			$model->getSession()->getVar('nonexistent')
		);
	}
	public function testGetingASetSessionVariableReturnsTheCorrectValue(){
		$model = new Model(
			new Request(
				new CookieContainer([]),
				new ServerContainer([]),
				new SessionContainer(['variable'=>'value'])
			)
		);
		$this->assertEquals(
			'value',
			$model->getSession()->getVar('variable')
		);
	}
	public function testSettingASessionVariableWorksAsExpected(){
		$model = new Model(
			new Request(
				new CookieContainer([]),
				new ServerContainer([]),
				new SessionContainer(['variable'=>'value'])
			)
		);
		$model->getSession()->setVar('otherVariable','otherValue');
		$this->assertEquals(
			'value',
			$model->getSession()->getVar('variable')
		);
		$this->assertEquals(
			'otherValue',
			$model->getSession()->getVar('otherVariable')
		);
	}
	public function testSettingASessionReferenceWorksAsExpected(){
		$sessionReferent = ['variable'=>'value','otherVariable'=>'otherValue'];
		$session = SessionContainer::createFromReference($sessionReferent);
		$model = new Model(
			new Request(
				new CookieContainer([]),
				new ServerContainer([]),
				$session
			)
		);
		$model->getSession()->setVar('variable','mutated');
		$model->getSession()->setVar('newVariable','newValue');
		$this->assertEquals(
			'mutated',
			$model->getSession()->getVar('variable')
		);
		$this->assertEquals(
			'otherValue',
			$model->getSession()->getVar('otherVariable')
		);
		$this->assertEquals(
			'newValue',
			$model->getSession()->getVar('newVariable')
		);
		$this->assertEquals(
			'mutated',
			$sessionReferent['variable']
		);
		$this->assertEquals(
			'otherValue',
			$sessionReferent['otherVariable']
		);
		$this->assertEquals(
			'newValue',
			$sessionReferent['newVariable']
		);
	}
	
	public function testGetingAnUnsetServerVariableReturnsNull(){
		$model = new Model(
			new Request(
				new CookieContainer([]),
				new ServerContainer([]),
				new SessionContainer([])
			)
		);
		$this->assertNull(
			$model->getServer()->getVar('nonexistent')
		);
	}
	public function testGetingASetServerVariableReturnsTheCorrectValue(){
		$model = new Model(
			new Request(
				new CookieContainer([]),
				new ServerContainer(['variable'=>'value']),
				new SessionContainer([])
			)
		);
		$this->assertEquals(
			'value',
			$model->getServer()->getVar('variable')
		);
	}
}
