<?php
//use standard serialize()/unserialize() functions to serialize sessions
ini_set("session.serialize_handler", "php_serialize");

//this file is almost verbatim from the PHP manual
class MySessionHandler implements SessionHandlerInterface
{
    private $savePath;

    public function open($savePath, $sessionName)
    {
        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }

        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
		if(!preg_match('/^[a-zA-Z0-9,-]{22,40}$/', $id)){
			die("Bad session ID.");
		}
        return (string)@file_get_contents("$this->savePath/sess_$id");
    }

    public function write($id, $data)
    {
		if(!preg_match('/^[a-zA-Z0-9,-]{22,40}$/', $id)){
			die("Bad session ID.");
		}
        return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
    }

    public function destroy($id)
    {
		if(!preg_match('/^[a-zA-Z0-9,-]{22,40}$/', $id)){
			die("Bad session ID.");
		}
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function gc($maxlifetime)
    {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
	
	//read all session data into an associative array by ID
	public function get_all(){
		$allsessions=array();
		$prefixLength=strlen("$this->savePath/sess_");
		foreach (glob("$this->savePath/sess_*") as $file) {
			$id=substr($file,$prefixLength);
			$blob=(string)@file_get_contents($file);
			$allsessions[$id]=@unserialize($blob);
        }
		return $allsessions;
	}
}

$handler = new MySessionHandler();
session_set_save_handler($handler, true);
