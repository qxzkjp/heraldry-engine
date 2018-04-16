<?php
namespace HeraldryEngine\AdminPanel;

use Exception;
use HeraldryEngine\DatabaseContainer;
use HeraldryEngine\Mvc\Controller;
use HeraldryEngine\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
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
    public function __construct(Silex\Application $app, Request $request){
        parent::__construct($app);
        $this->request = $request;
        $this->db = $app['db'];
    }
	public function deleteSession($id)
	{
		if ($id==session_id()) {
			$error = true;
		} else {
			$error = !$this->app['handler']->destroy($id);
		}

		if ($error) {
			$this->app['errorMessage']="Could not delete session";
		} else {
			$this->app['successMessage']="Session deleted successfully";
		}
	}

    /**
     * @param int $id
     */
    public function deleteUser($id)
	{
		$stmt = $this->db->prepareQuery(
			/** @lang MySQL */
            'DELETE FROM users WHERE ID = ?;'
		);
		$stmt->bind_param("i", $id);

		if (false === $stmt->execute()) {
			$this->app['errorMessage'] = "Could not delete user: database error";
		} else {
			$this->app['successMessage'] = "User id $id deleted successfully";
		}

		$stmt->close();
	}

    /**
     * @param int $id
     * @param int $accessLevel
     */
    public function changeUserAccess($id, $accessLevel)
	{
		if ($accessLevel >= 0 && $accessLevel <= 2) {
            $stmt = $this->db->prepareQuery(
				/** @lang MySQL */
                "UPDATE users SET accessLevel=? WHERE ID = ?;"
			);
			$stmt->bind_param("ii",$accessLevel, $id);

			if (false === $stmt->execute()) {
				$this->app['errorMessage'] = "Unable to change  user access level: database error.";
			} else {
				$this->app['successMessage'] = "User id $id access level changed successfully.";
			}

			$stmt->close();
		} else {
			$this->app['errorMessage'] = "Unable to change  user access level: unknown access level.";
		}
	}

    /**
     * @param string $userName
     * @param string $password
     * @param string $checkPassword
     * @param int $accessLevel
     * @return bool
     */
    public function createUser($userName, $password, $checkPassword, $accessLevel){
		if($password!=$checkPassword){
			$this->app['errorMessage'] =
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
				$this->app['successMessage'] = "User \"$userName\" created successfully.";
			}else{
				$this->app['errorMessage'] = "Could not create user \"$userName\". Name taken?";
				$this->app['debugMessage'] .= $this->db->getLastSqlError() . "<br/>";
			}
		}
		return $rtn;
	}

	public function changeUserPassword($id, $password, $checkPassword){
		$sesh=$this->app['session'];
		$rtn=false;
		if($id != $sesh['userID']){
			$errorPrefix="Could not change password for user id $id: ";
			$succMsg="Password for user ID $id changed successfully.";
		}else{
			$errorPrefix="Could not change password for your account: ";
			$succMsg="Password for your account changed successfully.";
		}
		if($id != $sesh['userID'] &&
			(!$sesh->offsetExists('accessLevel')
				|| $sesh['accessLevel'] > 0)
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
				$this->app['successMessage'] = $succMsg;
			}else{
				$this->app['errorMessage'] = $errorPrefix."database error.";
			}
			if($this->db->getLastSqlError() != ""){
				$this->app['debugMessage'] .=
                    $this->db->getLastSqlError() . "<br/>";
			}
		}
		return $rtn;
	}

	public function authenticateUser($uname, $pword){
		$uname=strtolower($uname);
		$ret=false;
        $stmt = $this->db->prepareQuery(
            /** @lang MySQL */
			"SELECT COUNT(*) FROM failureLogs ".
			"WHERE userName=? AND accessTime > (NOW() - INTERVAL 5 MINUTE);"
        );
		$stmt->bind_param("s", $uname);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_row();
		$attempts = $row[0];
		$stmt->close();
		if($attempts < 50){
            $stmt = $this->db->prepareQuery(
                /** @lang MySQL */
				"SELECT * FROM users WHERE userName = ?"
            );
			$stmt->bind_param("s", $uname);
			$stmt->execute();
			$result = $stmt->get_result();
			if($result->num_rows > 1){
				$this->app['debugMessage'] =
					"Login error: more than one user with the given username!";
			}else if($result->num_rows > 0){
				$row = $result->fetch_assoc();
				if(password_verify($pword, $row['pHash'])){
					$ret=$row;
				}
			}
			$stmt->close();
			if($ret === false){
				$this->app['errorMessage'] = "Wrong username or password.";
                $stmt = $this->db->prepareQuery(
                        /** @lang MySQL */
						"INSERT INTO failureLogs ".
						"(userName, accessTime, IP, isIPv6) ".
						"VALUES (?, NOW(), UNHEX(?), ?);"
                );
                $addr = $this->request->server->get('REMOTE_ADDR');
				$rawAddr = bin2hex(inet_pton($addr));
				$isIPv6 = filter_var(
							$addr,
							FILTER_VALIDATE_IP,
							FILTER_FLAG_IPV6
							)!==false;
				//var_dump($isIPv6);
				$stmt->bind_param(
					"ssi",
					$uname,
					$rawAddr,
					$isIPv6
					);
				$stmt->execute();
			}
		} else {
			$this->app->errorMessage = "You are rate limited. Chillax, bruh.";
		}
		return $ret;
	}

	public function createUserSession($row){
		$this->app['session']->setVar('userID',(int)$row['ID']);
		$this->app['session']->setVar('accessLevel',(int)$row['accessLevel']);
		$this->app['session']->setVar('userName',$row['userName']);
		$this->app['session']->setVar('startTime',time());
		$this->app['session']->setVar('userIP',
			$this->request->server->get('REMOTE_ADDR'));

		try {
            $parser = Parser::create();
        }catch(Exception $e){
            error_log($e->getMessage());
		    die();
        }
		$result = $parser->parse(
			$this->request->server->get('HTTP_USER_AGENT')
			);

		$this->app['session']->setVar('OS', $result->os->toString());
		$this->app['session']->setVar('browser',$result->ua->family);
		//get geolocation data from freegeoip (and drop line break)
		$this->app['session']->setVar(
			'geoIP',
			substr(file_get_contents(
				"https://freegeoip.net/csv/".
                $this->app['session']->getVar('userIP')
				), 0, -2)
			);
		$sections=explode(",",$this->app['session']->getVar('geoIP'));
		if($sections[2]!=""){
			$this->app['session']->setVar('countryName',$sections[2]);
		}
		if($sections[5]!=""){
			$this->app['session']->setVar('city',$sections[5]);
		}
		$this->app['security'] = new SecurityContext($this->app['session']);
		return true;
	}

	public function collectGarbage()
	{
		$this->app['session_handler']->gc(ini_get('session.gc_maxlifetime'));
		$this->app['successMessage']="Garbage collected successfully.";
	}
}
