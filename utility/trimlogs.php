<?php
$app = require 'bootstrap/bootstrap.php';

//no view, we don't display anything
use HeraldryEngine\AdminPanel\AdminPanelModel;
use HeraldryEngine\AdminPanel\AdminPanelController;

HeraldryEngine\PrivilegeCheck::requireAuth();

/**
 * @var array $config
 * @var SessionHandler $handler
 */
$config = $app['config'];
$handler = $app['session_handler'];

$model = new AdminPanelModel($config, $handler);
$controller = new AdminPanelController($model);

$controller->trimLogs();
