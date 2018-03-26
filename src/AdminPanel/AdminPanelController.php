<?php
namespace HeraldryEngine\AdminPanel;

use HeraldryEngine\Mvc\Controller;

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
	
	public function collectGarbage()
	{
		$this->model->handler->gc(ini_get('session.gc_maxlifetime'));
		$this->model->successMessage="Garbage collected successfully.";
	}
}
