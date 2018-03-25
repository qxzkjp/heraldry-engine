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

	public function collectGarbage()
	{
		$this->model->handler->gc(ini_get('session.gc_maxlifetime'));
		$this->model->successMessage="Garbage collected successfully.";
	}
}
