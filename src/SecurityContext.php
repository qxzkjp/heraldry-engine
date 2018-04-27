<?php
namespace HeraldryEngine;

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
     * @var string
     */
    private $CSRF;

    /**
     * SecurityContext constructor.
     * @param ClockInterface $clock
     * @param \DateInterval $lifetime
     * @param $params array|Session
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
        $this->CSRF = ArrayUtility::Get('CSRF', $params);
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
        if(isset($this->userName))
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
        if(isset($this->CSRF))
            $sesh->set('CSRF', $this->CSRF);
        $sesh->set('expiry', $this->expiry);
    }

    public function clone(SecurityContext $ctx){
        if(isset($ctx->id))
			$this->id = $ctx->id;
        else
            $this->id = 0;
        $this->clock = $ctx->clock;
        $this->startTime = $ctx->startTime;
        if(isset($ctx->browser))
			$this->browser = $ctx->browser;
        else
            $this->browser = "";
        if(isset($ctx->accessLevel))
			$this->accessLevel = $ctx->accessLevel;
        else
            $this->accessLevel = ACCESS_LEVEL_NONE;
        if(isset($ctx->userIP))
			$this->userIP = $ctx->userIP;
        else
            $this->userIP = "";
        if(isset($ctx->city))
			$this->city = $ctx->city;
        else
            $this->city = "";
        if(isset($ctx->countryName))
			$this->countryName = $ctx->countryName;
        else
            $this->countryName = "";
        $this->expiry = $ctx->expiry;
        if(isset($ctx->userName))
			$this->userName = $ctx->userName;
        else
            $this->userName = "";
        if(isset($ctx->OS))
			$this->OS = $ctx->OS;
        else
            $this->OS = "";
        if(isset($ctx->CSRF))
            $this->CSRF = $ctx->CSRF;
        else
            unset($this->CSRF);
    }

    /**
     * Taken from http://guid.us/GUID/PHP
     * @return string
     */
    private function getGUID()
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                . substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12)
                . chr(125);// "}"
            return $uuid;
        }
    }

    /**
     * @param Session $sesh
     * @return string
     */
    public function getCSRF(Session $sesh){
        if(!isset($this->CSRF)) {
            $guid = $this->getGUID();
            $this->CSRF = $guid;
            $this->StoreContext($sesh);
        }
        return $this->CSRF;
    }
}