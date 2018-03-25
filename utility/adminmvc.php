<?php
require "mvc.php";

class AdminPanelModel extends Model {
	public $mysqli;
	public $users;
	public $userRows;
	public $sessions;
	public $errorMessage;
	public $successMessage;
	public $handler;
	public function __construct(){
		require "connectvars.php";
		try {
			$this->mysqli = new mysqli($host, $dbUser, $dbPass, $dbName);
			$this->mysqli->set_charset("utf8mb4");
		} catch(Exception $e) {
			error_log($e->getMessage());
			exit('Error connecting to database'); //Should be a message a typical user could understand
		}
		$this->handler = $GLOBALS['handler'];
		$this->errorMsg="";
	}
	public function prepareModel(){
		$stmt = $this->mysqli->prepare("SELECT * FROM users");
		$stmt->execute();
		$result = $stmt->get_result();
		$this->users=array();
		$this->userRows=array();
		while($row = $result->fetch_assoc()) {
			array_push($this->userRows,$row);
			$this->users[(int)$row["ID"]]=$row["userName"];
		}
		$this->sessions = $this->handler->get_all();
	}
}

class AdminPanelView extends View {
	public function render(){
		$this->model->prepareModel();
		$this->setParam("users", $this->model->users);
		$this->setParam("userRows", $this->model->userRows);
		$this->setParam("sessionList", $this->model->sessions);
		$this->setParam("errorMessage", $this->model->errorMessage);
		$this->setParam("successMessage", $this->model->successMessage);
		parent::render();
	}
}

class AdminPanelController extends Controller{
	public function deleteSession($id){
		if($id==session_id()){
			$error=true;
		}else{
			$error = !$this->model->handler->destroy($id);
		}
		if($error){
			$this->model->errorMessage="Could not delete session";
		}else{
			$this->model->successMessage="Session deleted successfully";
		}
	}
	public function deleteUser($id){
		$stmt = $this->model->mysqli->prepare(
				"DELETE FROM users WHERE ID = ?;");
		$stmt->bind_param("i", $id);
		if(false === $stmt->execute()){
			$this->model->errorMessage=
				"Could not delete user: database error";
		}else{
			$this->model->successMessage=
				"User id $id deleted successfully";
		}
		$stmt->close();
	}
	public function changeUserAccess($id, $accessLevel){
		if($accessLevel>=0 && $accessLevel<=2){
			$stmt = $this->model->mysqli->prepare(
			"UPDATE users SET accessLevel=? WHERE ID = ?;");
			$stmt->bind_param("ii",$accessLevel, $id);
			if(false === $stmt->execute()){
				$this->model->errorMessage=
					"Unable to change  user access level: database error.";
			}else{
				$this->model->successMessage=
					"User id $id access level changed successfully.";
			}
			$stmt->close();
		}else{
			$this->model->errorMessage=
					"Unable to change  user access level: unknown access level.";
		}
	}
	public function collectGarbage(){
		$this->model->handler->gc(ini_get('session.gc_maxlifetime'));
		$this->model->successMessage="Garbage collected successfully.";
	}
}
?>