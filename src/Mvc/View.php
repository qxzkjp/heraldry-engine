<?php
namespace HeraldryEngine\Mvc;

/**
 * A view.
 */
class View
{
	/**
	 * Template file location.
	 *
	 * @var string
	 */
	private $template;

	/**
	 * View parameters.
	 *
	 * @var array
	 */
	private $params;

	/**
	 * The model.
	 *
	 * @var Model
	 */
	protected $model;

	/**
	 * The controller.
	 *
	 * TODO: Review. The view shouldn't need to know about the controller.
	 *
	 * @var Controller
	 */
	protected $controller;

	/**
	 * Create a new view.
	 *
	 * @param Controller $controller
	 * @param Model $model
	 */
	public function __construct($controller, $model)
	{
		$this->controller=$controller;
		$this->model=$model;
		$this->params=[];
	}

	/**
	 * Render the template.
	 */
	public function render()
	{
		$this->setParam("errorMessage", $this->model->errorMessage);
		$this->setParam("successMessage", $this->model->successMessage);
		$this->setParam("debugMessage", $this->model->debugMessage);
		if("" !== $this->model->getCookies()->getCookie("nightMode") ){
			$this->setParam("nightMode","true");
		}
		if(null !== $this->model->getSession()->getVar('userID')){
			$this->setParam("loggedIn","true");
		}
		require $this->template;
	}

	/**
	 * Set the template location.
	 *
	 * @param string $temp
	 */
	public function setTemplate($temp)
	{
		$this->template = $temp;
	}

	/**
	 * Set a parameter.
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function setParam($name, $value)
	{
		$this->params[$name] = $value;
	}

	/**
	 * Append a value to an array parameter.
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function appendParam($name, $value)
	{
		if(!isset($this->params[$name])){
			$this->params[$name]=[];
		}
		array_push($this->params[$name], $value);
	}
}
