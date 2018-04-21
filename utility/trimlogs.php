<?php
$app = require '../bootstrap/bootstrap.php';

//no view, we don't display anything
use HeraldryEngine\AdminPanel\AdminPanelModel;
use HeraldryEngine\AdminPanel\AdminPanelController;

\HeraldryEngine\Dbo\FailureLog::trimLogs($app);
