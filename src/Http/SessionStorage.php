<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 17/04/2018
 * Time: 21:30
 */

namespace HeraldryEngine\Http;


use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class SessionStorage extends NativeSessionStorage
{
    public function __construct(array $options = array(), $handler = null, MetadataBag $metaBag = null){
        $options = array_merge(
            [
                'serialize_handler' => 'php_serialize',
                'cookie_httponly' => true,
                'cookie_secure' => true,
                'cookie_domain' => '.heraldryengine.com'
            ],
            $options);
        parent::__construct($options, $handler, $metaBag);
    }
    public function start()
    {
        if ($this->started) {
            return true;
        }

        if (\PHP_SESSION_ACTIVE === session_status()) {
            throw new \RuntimeException('Failed to start the session: already started by PHP.');
        }

        if (ini_get('session.use_cookies') && headers_sent($file, $line)) {
            throw new \RuntimeException(sprintf('Failed to start the session because headers have already been sent by "%s" at line %d.', $file, $line));
        }

        $lifetime=(int)ini_get('session.gc_maxlifetime');

        // ok to try and start the session
        if (!session_start()) {
            throw new \RuntimeException('Failed to start the session');
        }
        //check for expired session
        if (array_key_exists("expiry",$_SESSION)) {
            if ($_SESSION["expiry"] < time()) {
                session_unset();
                $_SESSION['sessionExpired'] = true; //set flag to let us know previous session expired
            }else {
                $_SESSION["expiry"] = time() + $lifetime;
            }
        }
        setcookie(session_name(), session_id(), time() + $lifetime);

        $this->loadSession();

        return true;
    }
}