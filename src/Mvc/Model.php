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
	 * @var Array
	 */
	private $session;
	
	/**
	 * @var Array
	 */
	private $server;
	
	/**
	 * Create a new model.
	 */
	public function __construct($session=null, $server=null)
	{
		$this->session=$session;
		$this->server=$server;
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
	
	public function &getSession(){
		if(!isset($this->session)){
			return $_SESSION;
		}else{
			return $this->session;
		}
	}
	
	public function &getServer(){
		if(!isset($this->server)){
			return $_SERVER;
		}else{
			return $this->server;
		}
	}
}
