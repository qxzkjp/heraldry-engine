<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 17/04/2018
 * Time: 21:30
 */

namespace HeraldryEngine\Http;


use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\SessionBagProxy;
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

        setcookie(session_name(), session_id(), time() + $lifetime);
        $this->loadSession();

        $bags = $this->bags;
        /**
         * Now we set up session expiry in a (hopefully) forward-compatible way
         * @var SessionBagProxy $bag
         * @var AttributeBag $attrib
         * @var \DateTime $expiryDate
         */
        foreach ($bags as $bag) {
            if($bag->getBag() instanceof AttributeBag){
                $attrib = $bag->getBag();
                if($attrib->has('expiry')){
                    $expiryDate = $attrib->get('expiry');
                    if($expiryDate instanceof \DateTime) {
                        //TODO: expunge global state; lifetime should come from $app, and time() replace with $app['clock']
                        //check for expired session
                        if ($expiryDate->getTimestamp() < time()) {
                            session_unset();
                            session_regenerate_id();
                            $attrib->set('sessionExpired', true);
                        } else {
                            try {
                                $attrib->set('expiry', (new \DateTime())->add(new \DateInterval('PT' . $lifetime . 'S')));
                            } catch (\Exception $e) {
                                die('the impossible happened!');
                            }
                        }
                    }else{
                        $attrib->remove('expiry');
                    }
                }
            }
        }
        return true;
    }
}