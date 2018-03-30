<?php
namespace HeraldryEngine\AdminPanel;

use HeraldryEngine\Mvc\Controller;
use HeraldryEngine\UserAgentParser;
use UAParser\Parser;

class AdminPanelController extends Controller
{
	public function deleteSession($id)
	{
		if ($id==session_id()) {
			$error = true;
		} else {
			$error = !$this->model->handler->destroy($id);
		}

		if ($error) {
			$this->model->errorMessage="Could not delete session";
		} else {
			$this->model->successMessage="Session deleted successfully";
		}
	}

	public function deleteUser($id)
	{
		$stmt = $this->model->mysqli->prepare(
			"DELETE FROM users WHERE ID = ?;"
		);
		$stmt->bind_param("i", $id);

		if (false === $stmt->execute()) {
			$this->model->errorMessage = "Could not delete user: database error";
		} else {
			$this->model->successMessage = "User id $id deleted successfully";
		}

		$stmt->close();
	}

	public function changeUserAccess($id, $accessLevel)
	{
		if ($accessLevel >= 0 && $accessLevel <= 2) {
			$stmt = $this->model->mysqli->prepare(
				"UPDATE users SET accessLevel=? WHERE ID = ?;"
			);
			$stmt->bind_param("ii",$accessLevel, $id);

			if (false === $stmt->execute()) {
				$this->model->errorMessage = "Unable to change  user access level: database error.";
			} else {
				$this->model->successMessage = "User id $id access level changed successfully.";
			}

			$stmt->close();
		} else {
			$this->model->errorMessage = "Unable to change  user access level: unknown access level.";
		}
	}
	
	public function createUser($userName, $password, $checkPassword, $accessLevel){
		if($password!=$checkPassword){
			$this->model->errorMessage=
				"Could not create user \"$userName\": passwords did not match.";
			$rtn=false;
		}else{
			$pHash=password_hash($password,PASSWORD_DEFAULT);
			$stmt = $this->model->mysqli->prepare(
				"INSERT INTO users (userName, pHash, accessLevel) VALUES (?, ?, ?);");
			$stmt->bind_param("ssi", $userName, $pHash, $accessLevel);
			try{
				$rtn = $stmt->execute();
			}catch(Exception $e) {
				error_log($e->getMessage());
				$rtn=false;
			}
			if($rtn){
				$this->model->successMessage="User \"$userName\" created successfully.";
			}else{
				$this->model->errorMessage="Could not create user \"$userName\". Name taken?";
				$this->model->debugMessage .= $this->model->mysqli->error . "<br/>";
			}
		}
		return $rtn;
	}
	
	public function changeUserPassword($id, $password, $checkPassword){
		$sesh=$this->model->getSession();
		$rtn=false;
		if($id != $sesh['userID']){
			$errorPrefix="Could not change password for user id $id: ";
			$succMsg="Password for user ID $id changed successfully.";
		}else{
			$errorPrefix="Could not change password for your account: ";
			$succMsg="Password for your account changed successfully.";
		}
		if($id != $sesh['userID'] &&
			(!array_key_exists('accessLevel', $sesh)
				|| $sesh['accessLevel'] > 0)
		){
			$this->model->errorMessage = $errorPrefix . 
				"you are not an administrator.";
		}else if($password != $checkPassword){
			$this->model->errorMessage = $errorPrefix . 
				"passwords do not match.";
		}else{
			$pHash=password_hash($_POST["newPassword"], PASSWORD_DEFAULT);
			$stmt = $this->model->mysqli->prepare("UPDATE users SET pHash = ? WHERE ID = ?");
			$stmt->bind_param("si", $pHash, $id);
			try{
				$rtn = $stmt->execute();
			}catch(Exception $e) {
				error_log($e->getMessage());
				$rtn=false;
			}
			$stmt->close();
			if($rtn){
				$this->model->successMessage=$succMsg;
			}else{
				$this->model->errorMessage=$errorPrefix."database error.";
			}
			if($this->model->mysqli->error!=""){
				$this->model->debugMessage .= 
					$this->model->mysqli->error . "<br/>";
			}
		}
		return $rtn;
	}
	
	public function authenticateUser($uname, $pword){
		$uname=strtolower($uname);
		$ret=false;
		$stmt = $this->model->mysqli->prepare(
			"SELECT COUNT(*) FROM failureLogs ".
			"WHERE userName=? AND accessTime > (NOW() - INTERVAL 5 MINUTE);");
		$stmt->bind_param("s", $uname);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_row();
		$attempts = $row[0];
		$stmt->close();
		if($attempts < 50){
			$stmt = $this->model->mysqli->prepare(
				"SELECT * FROM users WHERE userName = ?");
			$stmt->bind_param("s", $uname);
			$stmt->execute();
			$result = $stmt->get_result();
			$ids=[];
			if($result->num_rows > 1){
				$this->model->debugMessage=
					"Login error: more than one user with the given username!";
			}else if($result->num_rows > 0){
				$row = $result->fetch_assoc();
				if(password_verify($pword,$row['pHash'])){
					$ret=$row;
				}
			}
			$stmt->close();
			if($ret === false){
				$this->model->errorMessage = "Wrong username or password.";
				$stmt = $this->model->mysqli->prepare(
						"INSERT INTO failureLogs ".
						"(userName, accessTime, IP, isIPv6) ".
						"VALUES (?, NOW(), UNHEX(?), ?);");
				//var_dump($this->model->mysqli->error);
				$rawAddr = bin2hex(inet_pton($this->model->getServer()['REMOTE_ADDR']));
				//var_dump($rawAddr);
				$isIPv6 = filter_var(
							$this->model->getServer()['REMOTE_ADDR'], 
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
			$this->model->errorMessage = "You are rate limited. Chillax, bruh.";
		}
		return $ret;
	}
	
	public function createUserSession($row){
		$this->model->getSession()->setVar('userID',(int)$row['ID']);
		$this->model->getSession()->setVar('accessLevel',(int)$row['accessLevel']);
		$this->model->getSession()->setVar('userName',$row['userName']);
		$this->model->getSession()->setVar('startTime',time());
		$this->model->getSession()->setVar('userIP',
			$this->model->getServer()->getVar('REMOTE_ADDR'));
		
		$parser = Parser::create();
		$result = $parser->parse(
			$this->model->getServer()->getVar('HTTP_USER_AGENT')
			);
		
		$this->model->getSession()->setVar('OS', $result->os->toString());
		$this->model->getSession()->setVar('browser',$result->ua->family);
		//get geolocation data from freegeoip (and drop line break)
		$this->model->getSession()->setVar(
			'geoIP',
			substr(file_get_contents(
				"https://freegeoip.net/csv/".
				$this->model->getSession()->getVar('userIP')
				), 0, -2)
			);
		$sections=explode(",",$this->model->getSession()->getVar('geoIP'));
		if($sections[2]!=""){
			$this->model->getSession()->setVar('countryName',$sections[2]);
		}
		if($sections[5]!=""){
			$this->model->getSession()->setVar('city',$sections[5]);
		}
		return true;
	}
	
	public function collectGarbage()
	{
		$this->model->handler->gc(ini_get('session.gc_maxlifetime'));
		$this->model->successMessage="Garbage collected successfully.";
	}
}
