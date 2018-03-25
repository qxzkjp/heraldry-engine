<?php
namespace HeraldryEngine\AdminPanel;

use HeraldryEngine\Mvc\Model;

class AdminPanelModel extends Model
{
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
