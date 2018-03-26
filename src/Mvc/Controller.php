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
	
	public function checkPrivNotLess($val){
		$sesh = &$this->model->getSession();
		return array_key_exists('accessLevel',$sesh)
			&& $sesh['accessLevel'] <= $val;
	}
}
