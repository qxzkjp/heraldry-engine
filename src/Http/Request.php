<?php
namespace HeraldryEngine\Http;

use HeraldryEngine\Http\SessionContainer;
use HeraldryEngine\Http\ServerContainer;
use HeraldryEngine\Http\CookieContainer;

class Request {
	/**
	* @var CookieContainer
	*/
	private $cookies;
	/**
	* @var ServerContainer
	*/
	private $server;
	/**
	* @var SessionContainer
	*/
	private $session;
	
	/**
	* @param CookieContainer $cookies
	* @param SessionContainer $session
	* @param ServerContainer $server
	*/
	public function __construct($cookies,$server,$session){
		$this->cookies=$cookies;
		$this->server=$server;
		$this->session=$session;
	}
	public static function createFromSuperGlobals(){
		return new Request(CookieContainer::createFromSuperGlobal(),
			ServerContainer::createFromSuperGlobal(),
			SessionContainer::createFromSuperGlobal());
	}
	public function getSession(){
		return $this->session;
	}
	public function getServer(){
		return $this->server;
	}
	public function getCookies(){
		return $this->cookies;
	}
}