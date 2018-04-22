<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 22/04/2018
 * Time: 16:27
 */

namespace HeraldryEngine\Http;


use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class Session extends \Symfony\Component\HttpFoundation\Session\Session
{
    /**
     * @var \DateInterval
     */
    protected $lifetime;
    /**
     * @var \HeraldryEngine\Interfaces\ClockInterface
     */
    protected $clock;
    public function __construct(\HeraldryEngine\Application $app, SessionStorageInterface $storage = null, AttributeBagInterface $attributes = null, FlashBagInterface $flashes = null)
    {
        $this->lifetime = $app['session_lifetime'];
        $this->clock = $app['clock'];
        parent::__construct($storage, $attributes, $flashes);
    }

    public function start()
    {
        $ret =  parent::start();
        if($ret && $this->has('expiry')){
            $expiryDate = $this->get('expiry');
            if($expiryDate instanceof \DateTime) {
                //check for expired session
                if ($expiryDate->getTimestamp() < $this->clock->__invoke()->getTimestamp()) {
                    $this->invalidate();
                    $this->set('sessionExpired', true);
                } else {
                    try {
                        $this->set('expiry', $this->clock->__invoke()->add($this->lifetime));
                    } catch (\Exception $e) {
                        die('the impossible happened!');
                    }
                }
            }else{
                $this->remove('expiry');
            }
        }
        return $ret;
    }
}