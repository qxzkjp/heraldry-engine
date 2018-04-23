<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 18/04/2018
 * Time: 19:41
 */

namespace HeraldryEngine;
use HeraldryEngine\Http\Session;
use HeraldryEngine\Http\SessionHandler;


/**
 * Application object for HE. Mainly exists for type annotations.
 * @property DatabaseContainer db
 * @property callable clock
 * @property Session session
 * @property int session_lifetime
 * @property SessionHandler session_handler
 * @property array config
 * @property SecurityContext security
 * @property array params
 */
class Application extends \Silex\Application
{
    /**
     * @var bool
     */
    private $hasCSRF = false;
    public function __construct(array $values = [])
    {
        $this['params'] = [];
        parent::__construct($values);
    }

    //http://guid.us/GUID/PHP
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

    public function getCSRF(){
        if(!$this->hasCSRF) {
            $guid = $this->getGUID();
            $this['session']->set('CSRF', $guid);
            $this->hasCSRF = true;
        }else{
            $guid = $this['session']->get('CSRF');
        }
        return $guid;
    }

    public function addParam($name, $value){
        $this['params'] = array_merge($this['params'],[$name => $value]);
    }

    public function __get($name)
    {
        return $this[$name];
    }
}
