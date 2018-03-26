<?php
namespace HeraldryEngine\Mvc;

/**
 * A model.
 */
class Model
{
	/**
	 * @var string
	 */
	public $errorMessage;

	/**
	 * @var string
	 */
	public $successMessage;
	
	/**
	 * @var string
	 */
	public $debugMessage;
	
	/**
	 * Create a new model.
	 */
	public function __construct()
	{
		$errorMessage="";
		$successMessage="";
		$debugMessage="";
	}

	/**
	 * Prepare the model.
	 */
	public function prepareModel()
	{

	}
}
