<?php
namespace HeraldryEngine;

use HeraldryEngine\Http\SessionContainer;
use HeraldryEngine\Utility\ArrayUtility;
use Symfony\Component\HttpFoundation\Session\Session;

define('ACCESS_LEVEL_ADMIN', 0);
define('ACCESS_LEVEL_USER', 1);
define('ACCESS_LEVEL_NONE', 2);

class SecurityContext {
    /**
     * @var int|null
     */
    private $id;
    /**
     * @var string|null
     */
	private $userName;
    /**
     * @var int|null
     */
	private $accessLevel;
    /**
     * @var int|null
     */
	private $startTime;
    /**
     * @var string|null
     */
	private $userIP;
    /**
     * @var string|null
     */
	private $countryName;
    /**
     * @var string|null
     */
	private $city;
    /**
     * @var int|null
     */
	private $expiry;

	private $clock;

    /**
     * SecurityContext constructor.
     * @param callable $clock
     * @param int $lifetime
     * @param $params array|SessionContainer|Session
     */
    public function __construct($clock, $lifetime, $params =[]){
        $this->id = ArrayUtility::Get('userID', $params);
        $this->userName = ArrayUtility::Get('userName', $params);;
        $this->accessLevel = ArrayUtility::Get('accessLevel', $params);
        $this->startTime = ArrayUtility::Get('startTime', $params, ($clock)());
        $this->userIP = ArrayUtility::Get('userIP', $params);
        $this->countryName = ArrayUtility::Get('countryName', $params);
        $this->city = ArrayUtility::Get('city', $params);
        $this->expiry = $this->startTime + $lifetime;
        $this->clock = $clock;
	}

	private function GetClock(){
        return ($this->clock)();
    }

    public function isExpired(){
        return ($this->GetClock()>$this->expiry);
    }

    /**
     * @return int
     */
    public function GetAccessLevel(){
        return (isset($this->accessLevel) && !$this->isExpired()) ? $this->accessLevel : ACCESS_LEVEL_NONE;
	}

    /**
     * @return string
     */
    public function GetUserName(){
        return (isset($this->userName) && !$this->isExpired()) ? $this->userName : '(unauthenticated)';
	}

    /**
     * @return int
     */
    public function GetUserID(){
        return (isset($this->id) && !$this->isExpired()) ? $this->id : 0;
	}

    /**
     * @return array
     */
    public function GetLocation(){
        if($this->isExpired()){
            return [];
        }
        $ret = [];
        if(isset($this->userIP)){
            $ret['userIP'] = $this->userIP;
        }
        if(isset($this->countryName)){
            $ret['countryName'] = $this->countryName;
        }
        if(isset($this->city)){
            $ret['city'] = $this->city;
        }
        return $ret;
    }

    public function StoreContext(Session $sesh){
        $sesh->set('userID', $this->id);
        $sesh->set('userName', $this->userName);
        $sesh->set('accessLevel', $this->accessLevel);
        $sesh->set('startTime', $this->startTime);
        $sesh->set('userIP', $this->userIP);
        $sesh->set('countryName', $this->countryName);
        $sesh->set('city', $this->city);
        $sesh->set('expiry', $this->expiry);
    }
}