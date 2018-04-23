<?php
namespace PHPSTORM_META {

    override(new \HeraldryEngine\Application,
        map([
            'params' => 'array',
            'config' => 'array',
            'entity_manager' => \Doctrine\ORM\EntityManager::class,
            'clock' => \HeraldryEngine\Interfaces\ClockInterface::class,
            'session_handler' => \HeraldryEngine\Http\SessionHandler::class,
            'session' => \Symfony\Component\HttpFoundation\Session\Session::class,
            'session_lifetime' => \DateInterval::class,
            'security' => \HeraldryEngine\SecurityContext::class,
            'db' => \HeraldryEngine\DatabaseContainer::class,
            'unsafe_post' => Symfony\Component\HttpFoundation\ParameterBag::class
        ]));
}
