<?php
namespace HeraldryEngine\Http;
use Silex;

class SessionContainer implements \ArrayAccess {
	/**
	* @var array
	*/
	private $vars;
	/**
	* @param array $sesh
	*/
	public function __construct($sesh){
		$this->vars = $sesh;
	}
	
	/**
	* @param array $ref
	*/
	private function setReference(&$ref){
		$this->vars = &$ref;
	}
	
	public static function createFromSuperGlobal(Silex\Application $app){
        // if a session isn't open, set one up
        if (session_status() === PHP_SESSION_NONE) {
            ini_set("session.serialize_handler", "php_serialize");
            session_set_save_handler($app['session_handler'], true);

            //custom session handler to allow reading sessions
            $lifetime=(int)ini_get('session.gc_maxlifetime');
            session_set_cookie_params($lifetime, '/', '.heraldryengine.com', true);
            session_start();
            if (array_key_exists("expiry",$_SESSION)) {
                if ($_SESSION["expiry"] < time()) {
                    session_unset();
                    session_destroy();
                    session_start();
                    $_SESSION['sessionExpired'] = true;
                }
            }
            $_SESSION["expiry"]=time() + $lifetime;
            setcookie(session_name(), session_id(), $_SESSION["expiry"]);
        }
		$ret=new SessionContainer([]);
		$ret->setReference($_SESSION);
		return $ret;
	}

    /**
     * @param string $varName
     * @return mixed|null
     */
	public function getVar($varName){
		if(array_key_exists($varName, $this->vars)){
			return $this->vars[$varName];
		}else{
			return null;
		}
	}
	
	/**
	* @param string $varName
	* @param mixed $newVal
	*/
	public function setVar($varName, $newVal){
		$this->vars[$varName]=$newVal;
	}

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists ( $offset ){
        return array_key_exists($offset, $this->vars);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet ( $offset ){
        if($this->offsetExists($offset)){
            return $this->getVar($offset);
        }else{
            return null;
        }
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet ($offset , $value ){
        $this->setVar($offset, $value);
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset ($offset){
        $this->setVar($offset, null);
    }
}
