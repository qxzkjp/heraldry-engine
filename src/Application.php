<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 18/04/2018
 * Time: 19:41
 */

namespace HeraldryEngine;
use HeraldryEngine\Http\SessionHandler;
use Silex;
use Symfony\Component\HttpFoundation\Session\Session;

class Application extends Silex\Application
{
    /**
     * @property DatabaseContainer db
     * @property callable clock
     * @property Session session
     * @property int session_lifetime
     * @property SessionHandler session_handler
     * @property array config
     * @property SecurityContext security
     * @property array params
     */
}