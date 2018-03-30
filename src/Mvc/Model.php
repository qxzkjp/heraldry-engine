<?php
namespace HeraldryEngine\Mvc;
use HeraldryEngine\Http\Request;

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
	 * @var HeraldryEngine\Http\Request
	 */
	private $request;
	
	/**
	 * Create a new model.
	 */
	public function __construct($request)
	{
		$this->request=$request;
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
	
	public function getSession(){
		return $this->request->getSession();
	}
	
	public function getServer(){
		return $this->request->getServer();
	}
	public function getCookies(){
		return $this->request->getCookies();
	}
}
