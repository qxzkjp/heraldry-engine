<?php
namespace HeraldryEngine\AdminPanel;

use Exception;
use HeraldryEngine\DatabaseContainer;
use HeraldryEngine\Mvc\Controller;
use HeraldryEngine\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use UAParser\Parser;
use Silex;

class AdminPanelController extends Controller
{
    /**
     * @var DatabaseContainer
     */
    private $db;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var \SessionHandlerInterface
     */
    private $sessionHandler;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var int
     */
    private $lifetime;
    public function __construct(Silex\Application $app, Request $request){
        parent::__construct($app);
        $this->request = $request;
        $this->db = $app['db'];
        $this->sessionHandler = $app['session_handler'];
        $this->session = $app['session'];
        $this->lifetime = $app['session_lifetime'];
    }
	public function deleteSession($app, $id)
	{
		if ($id==session_id()) {
			$error = true;
		} else {
			$error = !$this->sessionHandler->destroy($id);
		}

		if ($error) {
			$app['errorMessage'] = "Could not delete session";
		} else {
			$app['successMessage'] = "Session deleted successfully";
		}
	}

    /**
     * @param Silex\Application $app
     * @param int $id
     */
    public function deleteUser(Silex\Application $app, $id)
	{
		$stmt = $this->db->prepareQuery(
			/** @lang MySQL */
            'DELETE FROM users WHERE ID = ?;'
		);
		$stmt->bind_param("i", $id);

		if (false === $stmt->execute()) {
			$app['errorMessage'] = "Could not delete user: database error";
		} else {
			$app['successMessage'] = "User id $id deleted successfully";
		}

		$stmt->close();
	}

    /**
     * @param Silex\Application $app
     * @param int $id
     * @param int $accessLevel
     */
    public function changeUserAccess(Silex\Application $app, $id, $accessLevel)
	{
		if ($accessLevel >= 0 && $accessLevel <= 2) {
            $stmt = $this->db->prepareQuery(
				/** @lang MySQL */
                "UPDATE users SET accessLevel=? WHERE ID = ?;"
			);
			$stmt->bind_param("ii",$accessLevel, $id);

			if (false === $stmt->execute()) {
				$app['errorMessage'] = "Unable to change  user access level: database error.";
			} else {
				$app['successMessage'] = "User id $id access level changed successfully.";
			}

			$stmt->close();
		} else {
			$app['errorMessage'] = "Unable to change  user access level: unknown access level.";
		}
	}

    /**
     * @param Silex\Application $app
     * @param string $userName
     * @param string $password
     * @param string $checkPassword
     * @param int $accessLevel
     * @return bool
     */
    public function createUser(Silex\Application $app, $userName, $password, $checkPassword, $accessLevel){
		if($password!=$checkPassword){
			$app['errorMessage'] =
				"Could not create user \"$userName\": passwords did not match.";
			$rtn = false;
		}else{
			$pHash=password_hash($password,PASSWORD_DEFAULT);
            $stmt = $this->db->prepareQuery(
				/** @lang MySQL */
                "INSERT INTO users (userName, pHash, accessLevel) VALUES (?, ?, ?);"
            );
			$stmt->bind_param("ssi", $userName, $pHash, $accessLevel);
			try{
				$rtn = $stmt->execute();
			}catch(Exception $e) {
				error_log($e->getMessage());
				$rtn = false;
			}
			if($rtn){
				$app['successMessage'] = "User \"$userName\" created successfully.";
			}else{
				$app['errorMessage'] = "Could not create user \"$userName\". Name taken?";
				$app['debugMessage'] .= $this->db->getLastSqlError() . "<br/>";
			}
		}
		return $rtn;
	}

    /**
     * @param Silex\Application $app
     * @param int $id
     * @param string $password
     * @param string $checkPassword
     * @return bool
     */
    public function changeUserPassword(Silex\Application $app, $id, $password, $checkPassword){
		$sesh=$this->session;
		$rtn=false;
		if($id != $sesh->get('userID')){
			$errorPrefix="Could not change password for user id $id: ";
			$succMsg="Password for user ID $id changed successfully.";
		}else{
			$errorPrefix="Could not change password for your account: ";
			$succMsg="Password for your account changed successfully.";
		}
		if($id != $sesh['userID'] &&
			($sesh->has('accessLevel')
				|| $sesh->get('accessLevel') > 0)
		){
			$this->app->errorMessage = $errorPrefix .
				"you are not an administrator.";
		}else if($password != $checkPassword){
			$this->app->errorMessage = $errorPrefix .
				"passwords do not match.";
		}else{
			$pHash=password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepareQuery(
                /** @lang MySQL */
            "UPDATE users SET pHash = ? WHERE ID = ?"
            );
			$stmt->bind_param("si", $pHash, $id);
			try{
				$rtn = $stmt->execute();
			}catch(Exception $e) {
				error_log($e->getMessage());
				$rtn = false;
			}
			$stmt->close();
			if($rtn){
				$app['successMessage'] = $succMsg;
			}else{
				$app['errorMessage'] = $errorPrefix."database error.";
			}
			if($this->db->getLastSqlError() != ""){
				$app['debugMessage'] .=
                    $this->db->getLastSqlError() . "<br/>";
			}
		}
		return $rtn;
	}

    /**
     * @param Silex\Application $app
     * @param SecurityContext $ctx
     * @return bool
     */
    public function createUserSession(Silex\Application $app, SecurityContext $ctx){
		$ctx->StoreContext($app['session']);
		return true;
	}

    /**
     * @param Silex\Application $app
     */
    public function collectGarbage(Silex\Application $app)
	{
		$this->sessionHandler->gc($this->lifetime);
		$app['successMessage']="Garbage collected successfully.";
	}
}
