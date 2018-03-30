<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use HeraldryEngine\Mvc\Model;

final class ModelTestCase extends TestCase{
	public function testGetUnsetCookie(){
		$model = new Model([],[],[]);
		$this->assertEquals(
			'',
			$model->getCookie('nonexistent')
		);
	}
	public function testGetSetCookie(){
		$model = new Model([],[],['cookie'=>'value']);
		$this->assertEquals(
			'value',
			$model->getCookie('cookie')
		);
	}
}
