<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 18/04/2018
 * Time: 19:41
 */

namespace HeraldryEngine;
use HeraldryEngine\Http\SessionHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Application object for HE. Mainly exists for type annotations.
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
    public function __construct(array $values = [])
    {
        $this['params'] = [];
        parent::__construct($values);
    }

    public function addParam($name, $value){
        $this['params'] = array_merge($this['params'],[$name => $value]);
    }

    public function run(Request $request = null){
        if(!isset($request))
            $request = Request::createFromGlobals();
        if( !$request->hasSession() && isset($this['session']))
            $request->setSession($this['session']);
        parent::run($request);
    }

    public function __get($name)
    {
        return $this[$name];
    }
}
