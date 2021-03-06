<?php
namespace HeraldryEngine\AdminPanel;

use HeraldryEngine\Mvc\View;

class AdminPanelView extends View
{
	public function render()
	{
		$this->model->prepareModel();
		$this->setParam("users", $this->model->users);
		$this->setParam("userRows", $this->model->userRows);
		$this->setParam("sessionList", $this->model->sessions);
		return parent::render();
	}
}
