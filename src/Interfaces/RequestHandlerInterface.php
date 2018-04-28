<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 28/04/2018
 * Time: 01:26
 */

namespace HeraldryEngine\Interfaces;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

interface RequestHandlerInterface
{
    /**
     * @param Request $req
     * @param \int $type
     * @param \bool $catch
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function Handle(Request $req,
                           $type = HttpKernelInterface::MASTER_REQUEST,
                           $catch = true) : Response;

    /**
     * @param \string $url
     * @param \int $status
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function Redirect($url, $status = 302) : Response;
}