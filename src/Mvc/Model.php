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
	public function __construct($session=null, $server=null, $cookies=null)
	{
		$this->session=$session;
		$this->server=$server;
		$this->cookies=$cookies;
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
	public function getCookie($str){
		if(!isset($this->cookies)){
			if(array_key_exists($str,$_COOKIE)){
				return $_COOKIE[$str];
			}else{
				return "";
			}
			
		}else{
			if(array_key_exists($str,$this->cookies)){
				return $this->cookies[$str];
			}else{
				return "";
			}
		}
	}
}
