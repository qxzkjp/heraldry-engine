<?php
namespace HeraldryEngine\Mvc;
use HeraldryEngine\Http\Request;
use Silex;
use Symfony;

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
	 * The application object
	 *
	 * @var \HeraldryEngine\Application
	 */
	protected $app;

    /**
     * The request object
     *
     * @var Request
     */
	protected $request;

	/**
	 * Create a new view.
	 *
	 * @param \HeraldryEngine\Application $model
	 * @param Symfony\Component\HttpFoundation\Request $request
	 */
	public function __construct(&$model, &$request)
	{
		$this->app=&$model;
		$this->request=&$request;
		$this->params=[];
	}

	/**
	 * Render the template.
	 */
	public function render()
	{
	    $this->params['CSRF'] = $this->app->getCSRF();
	    $this->params = array_merge($this->params, $this->app['params']);
		if("" !== $this->request->cookies->get("nightMode","") ){
			$this->setParam("nightMode","true");
		}
		if(null !== $this->app['session']->get('userID')){
			$this->setParam("loggedIn","true");
		}
		$this->setParam('path', $this->request->getRequestUri());
		$app = $this->app;
		$params = $this->params;
		ob_start();
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
