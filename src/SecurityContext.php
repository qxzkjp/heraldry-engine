<?php
namespace HeraldryEngine;

use HeraldryEngine\Http\SessionContainer;
use HeraldryEngine\Utility\ArrayUtility;

define('ACCESS_LEVEL_ADMIN', 0);
define('ACCESS_LEVEL_USER', 1);
define('ACCESS_LEVEL_NONE', 2);

class SecurityContext {
	private $id;
	private $userName;
	private $accessLevel;

    /**
     * SecurityContext constructor.
     * @param $params array|SessionContainer
     */
    public function __construct($params){
		if( ArrayUtility::OffsetExists('UserID', $params) ){
			$this->id = $params['userID'];
		}
		if( ArrayUtility::OffsetExists('UserName', $params) ){
			$this->userName = $params['userName'];
		}
		if( ArrayUtility::OffsetExists('accessLevel', $params) ){
			$this->accessLevel = $params['accessLevel'];
		}
	}

    /**
     * @return int
     */
    public function GetAccessLevel(){
        return isset($this->accessLevel) ? $this->accessLevel : ACCESS_LEVEL_NONE;
	}

    /**
     * @return string
     */
    public function GetUserName(){
        return isset($this->userName) ? $this->userName : '(unauthenticated)';
	}

    /**
     * @return int
     */
    public function GetUserID(){
        return isset($this->id) ? $this->id : 0;
	}
}