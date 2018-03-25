<?php
namespace HeraldryEngine\Mvc;

/**
 * A controller.
 */
class Controller
{
	/**
	 * @var Model
	 */
	protected $model;

	/**
	 * Create a new controller.
	 *
	 * @param Model $model
	 */
	public function __construct($model)
	{
		$this->model=$model;
	}
}
