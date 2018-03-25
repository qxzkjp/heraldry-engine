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
	 * @var
	 */
	protected $controller;

	/**
	 * Create a new view.
	 *
	 * @param Controller $controller
	 * @param Model $model
	 */
	public function __construct($controller,$model)
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
	public function appendParam($name, $value){
		array_push($this->params[$name], $value);
	}
}
