<?php
namespace HeraldryEngine\Http;
class ServerContainer{
	/**
	 * @var array
	 */
	private $serverVars;
	
	/**
	* constructs a ServerContainer from an array of server variables
	*
	* @param array $arr Array of variables
	*/
	public function __construct($arr){
		$this->serverVars=$arr;
	}
	
	public static function createFromSuperGlobal(){
		return new ServerContainer($_SERVER);
	}
	
	/**
	* gets a variable from a server variable container
	*
	* @param string $name Name of variable to get
	*/
	public function getVar($name){
		if(array_key_exists($name,$this->serverVars)){
			return $this->serverVars[$name];
		}else{
			return null;
		}
	}
}
