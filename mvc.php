<?php
class View {
	private $template;
	private $params;
	private $model;
	private $controller;
	public function __construct($controller,$model){
		$this->controller=$controller;
		$this->model=$model;
		$this->params=[];
	}
	public function render(){
		require $this->template;
	}
	public function setTemplate($temp){
		$this->template = $temp;
	}
	public function setParam($name, $value){
		$this->params[$name] = $value;
	}
	public function appendParam($name, $value){
		array_push($this->params[$name], $value);
	}
}
class Model {
	public function __construct(){
	}
}
class Controller {
	private $model;
	public function __construct($model){
		$this->model=$model;
	}
}
?>