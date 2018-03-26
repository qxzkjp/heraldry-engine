<?php
namespace HeraldryEngine\AdminPanel;

use Exception;
use HeraldryEngine\Mvc\Model;
use HeraldryEngine\SessionHandler;
use mysqli;

class AdminPanelModel extends Model
{
	/**
	 * @var mysqli
	 */
	public $mysqli;

	/**
	 * @var array
	 */
	public $users;

	/**
	 * @var array
	 */
	public $userRows;

	/**
	 * @var array
	 */
	public $sessions;

	/**
	 * @var string
	 */
	public $errorMessage;

	/**
	 * @var string
	 */
	public $successMessage;
	
	/**
	 * @var string
	 */
	public $debugMessage;

	/**
	 * @var SessionHandler
	 */
	public $handler;

	/**
	 * Create a new admin panel model.
	 *
	 * @param array $config
	 * @param SessionHandler $handler
	 */
	public function __construct($config, SessionHandler $handler)
	{
		try {
			$this->mysqli = new mysqli(
				$config['db.host'],
				$config['db.user'],
				$config['db.pass'],
				$config['db.name']
			);
			$this->mysqli->set_charset("utf8mb4");
		} catch(Exception $e) {
			error_log($e->getMessage());
			exit('Error connecting to database'); //Should be a message a typical user could understand
		}

		$this->handler = $handler;
		$this->errorMessage="";
		$this->successMessage="";
		$this->debugMessage="";
	}

	/**
	 * Prepare the model.
	 */
	public function prepareModel()
	{
		$stmt = $this->mysqli->prepare("SELECT * FROM users");
		$stmt->execute();
		$result = $stmt->get_result();
		$this->users=array();
		$this->userRows=array();

		while ($row = $result->fetch_assoc()) {
			array_push($this->userRows,$row);
			$this->users[(int)$row["ID"]]=$row["userName"];
		}

		$this->sessions = $this->handler->get_all();
	}
}
