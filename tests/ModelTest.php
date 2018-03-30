<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use HeraldryEngine\Mvc\Model;
use HeraldryEngine\Http\Request;
use HeraldryEngine\Http\SessionContainer;
use HeraldryEngine\Http\ServerContainer;
use HeraldryEngine\Http\CookieContainer;

final class ModelTestCase extends TestCase{
	public function testGetUnsetCookie(){
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
	public function testGetSetCookie(){
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
}
