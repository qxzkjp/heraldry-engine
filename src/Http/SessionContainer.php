<?php
namespace HeraldryEngine\Http;
class SessionContainer{
	/**
	* @var array
	*/
	private $vars;
	/**
	* @param array $sesh
	*/
	public function __construct($sesh){
		$this->vars = $sesh;
	}
	
	/**
	* @param array $ref
	*/
	private function setReference(&$ref){
		$this->vars = &$ref;
	}
	
	public static function createFromSuperGlobal(){
		$ret=new SessionContainer([]);
		$ret->setReference($_SESSION);
		return $ret;
	}
	
	/**
	* @param string $varName
	*/
	public function getVar($varName){
		if(array_key_exists($varName, $this->vars)){
			return $this->vars[$varName];
		}else{
			return null;
		}
	}
	
	/**
	* @param string $varName
	* @param mixed $newVal
	*/
	public function setVar($varName,$newVal){
		$this->vars[$varName]=$newVal;
	}
}
