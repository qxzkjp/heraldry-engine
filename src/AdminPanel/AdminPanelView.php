<?php
namespace HeraldryEngine\AdminPanel;

use HeraldryEngine\Mvc\View;

class AdminPanelView extends View
{
	public function render()
	{
		$this->setParam("users", $this->app['params']['users']);
		$this->setParam("userRows", $this->app['params']['userRows']);
		$this->setParam("sessionList", $this->app['params']['sessions']);
		return parent::render();
	}
}
