<?php
namespace HeraldryEngine;

use HeraldryEngine\Http\SessionContainer;
use HeraldryEngine\Interfaces\ClockInterface;
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
     * @var \DateTime|null
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
    /**
     * @var ClockInterface
     */
	private $clock;
    /**
     * @var string
     */
    private $OS;
    /**
     * @var string
     */
    private $browser;

    /**
     * SecurityContext constructor.
     * @param ClockInterface $clock
     * @param \DateInterval $lifetime
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
        $this->OS = ArrayUtility::Get('OS', $params);
        $this->browser = ArrayUtility::Get('browser', $params);
        $this->expiry = $this->startTime;
        $this->expiry->add($lifetime);
        $this->clock = $clock;
	}

    /**
     * @return \DateTime
     */
    private function GetClock(){
        return ($this->clock)();
    }

    public function isExpired(){
        $now = $this->GetClock()->getTimestamp();
        if($now === false) //if time has overflowed, assume we've expires
            return true;
        return ( $now > $this->expiry->getTimestamp());
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
     * @return string
     */
    public function getUserIP(): string
    {
        return (isset($this->userIP) && !$this->isExpired())?$this->userIP:"";
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

    /**
     * @return string
     */
    public function getBrowser(): string
    {
        return (isset($this->browser) && !$this->isExpired())?$this->browser:"";
    }

    /**
     * @return string
     */
    public function getOS(): string
    {
        return (isset($this->OS) && !$this->isExpired())?$this->OS:"";
    }

    /**
     * @param Session $sesh
     */
    public function StoreContext(Session $sesh){
        $sesh->set('userID', $this->id);
        $sesh->set('userName', $this->userName);
        $sesh->set('accessLevel', $this->accessLevel);
        $sesh->set('startTime', $this->startTime);
        $sesh->set('userIP', $this->userIP);
        if(isset($this->countryName))
            $sesh->set('countryName', $this->countryName);
        if(isset($this->city))
            $sesh->set('city', $this->city);
        if(isset($this->OS))
            $sesh->set('OS', $this->OS);
        if(isset($this->browser))
            $sesh->set('browser', $this->browser);
        $sesh->set('expiry', $this->expiry);
    }
}