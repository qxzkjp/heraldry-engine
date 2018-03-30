<?php
$app = require 'bootstrap/bootstrap.php';

use HeraldryEngine\AdminPanel\AdminPanelModel;
use HeraldryEngine\AdminPanel\AdminPanelView;
use HeraldryEngine\AdminPanel\AdminPanelController;

/**
 * @var array $config
 * @var SessionHandler $handler
 */
$config = $app['config'];
$handler = $app['session_handler'];

$model = new AdminPanelModel($config, $handler, $app['request']);
$controller = new AdminPanelController($model);

for( $i=0; $i<100; $i++){
	$stmt = $model->mysqli->prepare(
		"INSERT INTO failureLogs (userName, accessTime, IP, isIPv6) VALUES ('donkey', NOW(), INET6_ATON(?), IS_IPV6(?));");
	$addr="FFFF:FFFF:FFFF:FFFF::1";
	$stmt->bind_param("ss", $addr, $addr);
	$stmt->execute();
}
