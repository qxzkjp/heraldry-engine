<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 25/04/2018
 * Time: 18:48
 */

namespace HeraldryEngine\Http;


use HeraldryEngine\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class Gpc
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function CheckCsrf(Request $req, Session $sesh){
        return ($this->app->security->getCSRF($sesh) == $req->request->get('CSRF'));
    }

    /**
     * @param Request $req
     * @param string $key
     * @return mixed|null
     */
    public function Post(Request $req, string $key){
        if($this->app->session->get('CSRF') == $req->request->get('CSRF'))
            return $req->request->get($key);
        else
            return null;
    }

    /**
     * @param Request $req
     * @param string $key
     * @return bool
     */
    public function PostHas(Request $req, string $key){
        if($this->app->session->get('CSRF') == $req->request->get('CSRF'))
            return $req->request->has($key);
        else
            return false;
    }

    public function UnsafePost(Request $req, string $key){
        return $req->request->get($key);
    }

    public function UnsafePostHas(Request $req, string $key){
        return $req->request->has($key);
    }

    /**
     * @param Request $req
     * @param string $key
     * @return mixed
     */
    public function Get(Request $req, string $key){
        return $req->query->get($key);
    }

    /**
     * @param Request $req
     * @param string $key
     * @return bool
     */
    public function GetHas(Request $req, string $key){
        return $req->query->has($key);
    }

    public function Cookie(Request $req, string $key){
        return $req->cookies->get($key);
    }

    public function CookieHas(Request $req, string $key){
        return $req->cookies->has($key);
    }

}