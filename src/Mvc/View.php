<?php
namespace HeraldryEngine\Mvc;
use HeraldryEngine\Interfaces\ClockInterface;
use HeraldryEngine\SecurityContext;
use Symfony;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * A view.
 */
class View
{
	/**
	 * Template file location.
	 *
	 * @var string
	 */
	private $template;

	/**
	 * View parameters.
	 *
	 * @var array
	 */
	private $params;

    /**
     * The request object
     *
     * @var Request
     */
	//protected $request;

    /**
     * Create a new view.
     */
	public function __construct()
	{
		$this->params=[];
	}

    /**
     * Render the template.
     * @param Request $request
     * @param SecurityContext $ctx
     * @param ClockInterface $clock
     * @param Session $sesh
     * @param $params
     * @return string
     */
	public function render(Request $request, SecurityContext $ctx, ClockInterface $clock, Session $sesh, $params)
	{
	    $this->params['CSRF'] = $ctx->getCSRF($sesh);
	    $this->params = array_merge($this->params, $params);
		if("" !== $request->cookies->get("nightMode","") ){
			$this->setParam("nightMode","true");
		}
		if($ctx->GetUserID() != 0){
			$this->setParam("loggedIn","true");
		}
		$this->setParam('path', $request->getRequestUri());
        /** @noinspection PhpUnusedLocalVariableInspection */
        $params = $this->params;
        /** @noinspection PhpUnusedLocalVariableInspection */
        /** @noinspection PhpSillyAssignmentInspection */
        $clock = $clock;
		ob_start();
        /** @noinspection PhpIncludeInspection */
        require $this->template;
		return ob_get_clean();
	}

	/**
	 * Set the template location.
	 *
	 * @param string $temp
	 */
	public function setTemplate($temp)
	{
		$this->template = $temp;
	}

	/**
	 * Set a parameter.
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function setParam($name, $value)
	{
		$this->params[$name] = $value;
	}

	/**
	 * Append a value to an array parameter.
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function appendParam($name, $value)
	{
		if(!isset($this->params[$name])){
			$this->params[$name]=[];
		}
		array_push($this->params[$name], $value);
	}
}
