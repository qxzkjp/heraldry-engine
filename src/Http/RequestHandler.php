<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 27/04/2018
 * Time: 20:54
 */

namespace HeraldryEngine\Http;


use HeraldryEngine\Application;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestHandler
{
    /**
     * @var Application
     */
    private $app;

    /**
     * RequestHandler constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @param int $type
     * @param bool $catch
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function Handle(\Symfony\Component\HttpFoundation\Request $req,
                           $type = HttpKernelInterface::MASTER_REQUEST,
                           $catch = true){
        return $this->app->handle($req, $type, $catch);
    }

    /**
     * @param $url
     * @param int $status
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function Redirect($url, $status = 302){
        return $this->app->redirect($url, $status);
    }
}