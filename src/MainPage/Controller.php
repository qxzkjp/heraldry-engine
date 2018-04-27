<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 24/04/2018
 * Time: 19:30
 */

namespace HeraldryEngine\MainPage;


use HeraldryEngine\Application;
use HeraldryEngine\Http\Gpc;
use HeraldryEngine\Mvc\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    /**
     * @var Application
     */
    private $app;

    /**
     * Controller constructor.
     * @param Application $app
     */
    public function __construct(Application $app){
        $this->app = $app;
    }

    /**
     * @param Request $req
     * @param Gpc $gpc
     * @return Response
     */
    public function Show(Request $req){
        $view = new View();
        $view->setTemplate("templates/template.php");
        $view->setParam("content","blazon.php");
        $view->setParam("primaryHead","Heraldry");
        $view->setParam("secondaryHead","Engine");
        $view->setParam("scriptList",[
            "vendor/path-data-polyfill",
            "cubic",
            "syntax",
            "svg",
            "ui",
            "blazon",
            "enable"
        ]);
        $view->setParam("cssList",[
            [
                "name" => "narrow"
            ],
            [
                "name" => "heraldry-not-shit",
                "id" => "heraldry-css"
            ]]);
        $view->setParam("menuList",[
            [
                "href" => "/readme.md",
                "label" => "What is this?"
            ],
            [
                "href" => "#",
                "label" => "Example blazons",
                "expandable" => "demoBlazons.php"
            ],
            [
                "href" => "#",
                "label" => "Toggle syntax display",
                "id" => "toggleSyntax",
                "toggle" => true
            ],
            [
                "href" => "https://github.com/qxzkjp",
                "label" => "GitHub page"
            ],
            [
                "href" => "/download",
                "label" => "Download Blazon",
                "id" => "downloadButton"
            ]
        ]);
        if($this->app['security']->GetAccessLevel()==ACCESS_LEVEL_ADMIN){
            $view->appendParam("menuList",[
                "href" => "/admin",
                "label" => "Secret admin shit"
            ]);
        }else{
            $view->appendParam("menuList",[
                "href" => "/changepassword",
                "label" => "Change password"
            ]);
        }
        $view->setParam("demoBlazons", [
            [
                "label" => "<i>Scrope v Grosvenor</i> (arms of Baron Scrope)",
                "blazon" => "Azure, a bend Or"
            ],
            [
                "label" => "Arms of the town of Gerville, France",
                "blazon" => "Argent, on a bend Azure between two phrygian caps Gules three mullets of six points Or"
            ],
            [
                "label" => "Old arms of France",
                "blazon" => "Azure semy of fleurs-de-lys Or"
            ],
            [
                "blazon" => "Per pale Gules and Azure, on a bend sinister between two fleurs-de-lys Or three keys palewise Purpure"
            ],
            [
                "blazon" => "Per pale Azure on a bend between two mullets Or three roundels Vert and Argent three phrygian caps Gules"
            ],
            [
                "blazon" => "Per pale Sable and Or, three roundels counterchanged"
            ]
        ]);
        return new Response($view->render($req, $this->app->security, $this->app->clock, $this->app->session, $this->app->params), Response::HTTP_OK);
    }
}