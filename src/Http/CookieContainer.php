<?php
namespace HeraldryEngine\Http;
class CookieContainer{
	/**
	 * @var array
	 */
	private $cookies;
	
	/**
	* constructs a cookieContainer from an array of cookies
	*
	* @param array $arr Array of cookies
	*/
	public function __construct($arr){
		$this->cookies=$arr;
	}
	
	public static function createFromSuperGlobal(){
		return new CookieContainer($_COOKIE);
	}
	
	/**
	* gets a cookie from a cookie container
	*
	* @param string $name Name of cookie to get
	*/
	public function getCookie($name){
		if(array_key_exists($name,$this->cookies)){
			return $this->cookies[$name];
		}else{
			return "";
		}
	}
}