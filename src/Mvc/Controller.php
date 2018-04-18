<?php
namespace HeraldryEngine\Mvc;
use Silex;

/**
 * A controller.
 */
class Controller
{
	/**
	 * @var Model
	 */
	protected $app;

	/**
	 * Create a new controller.
	 *
	 * @param Silex\Application $app
	 */
	public function __construct(&$app)
	{
		$this->app=&$app;
	}
	
	public function checkPrivNotLess($val){
		$al=$this->app['session']->getVar('accessLevel');
		return (isset($al) && $al <= $val);
	}
	
	//http://guid.us/GUID/PHP
	private function getGUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12)
            .chr(125);// "}"
        return $uuid;
    }
}
	
	public function createGUID(){
		$this->app['session']->set('UUID', $this->getGUID());
		return $this->app['session']->get('UUID');
	}
	
	public function checkGUID($uuid){
		if($this->app['session']->get('UUID')==''){
			return false;
		}
		$ret = $this->app['session']->get('UUID') == $uuid;
		//UUID is burned up once used
		//if($ret){
			//this was good security policy, but it took too long to make work
			//$this->model->getSession()->setVar('UUID','');
		//}
		return $ret;
	}
}
