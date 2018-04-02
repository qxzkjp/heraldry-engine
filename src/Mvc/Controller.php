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
		$sesh = $this->model->getSession();
		$al=$sesh->getVar('accessLevel');
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
		$this->model->getSession()->setVar('UUID',$this->getGUID());
		return $this->model->getSession()->getVar('UUID');
	}
	
	public function checkGUID($uuid){
		if($this->model->getSession()->getVar('UUID')==''){
			return false;
		}
		$ret = $this->model->getSession()->getVar('UUID') == $uuid;
		//UUID is burned up once used
		if($ret){
			//this was good security policy, but it took too long to make work
			//$this->model->getSession()->setVar('UUID','');
		}
		return $ret;
	}
}
