@ECHO OFF
CALL ./vendor/bin/phpunit -v --bootstrap ./vendor/autoload.php --testdox tests
PAUSE
