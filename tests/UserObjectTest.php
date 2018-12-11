<?php

use HeraldryEngine\Dbo\User;
use HeraldryEngine\Utility\RandomString;
use PHPUnit\Framework\TestCase;

class UserObjectTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCheckingUserPasswordWorks(){
        for($i=0; $i<100;++$i) {
            $userName = RandomString::generate(mt_rand(1, 49));
            $password = RandomString::generate(mt_rand(0, 100));
            $usr = new User($userName, $password, 1);
            $this->assertTrue(
                $usr->checkPassword($password),
                "Password verification failed!\nusername= $userName\npassword= $password"
            );
        }
    }

    /**
     * @throws Exception
     */
    public function testCheckingUserPasswordWithNoUsernameWorks(){
        for($i=0; $i<100;++$i) {
            $userName = "";
            $password = RandomString::generate(mt_rand(0, 100));
            $usr = new User($userName, $password, 1);
            $this->assertTrue(
                $usr->checkPassword($password),
                'Password verification failed!\n'
            );
        }
    }

    /**
     * @throws Exception
     */
    public function testCheckingUserPasswordWithMaxLengthUsernameWorks(){
        for($i=0; $i<100;++$i) {
            $userName = RandomString::generate(50);
            $password = RandomString::generate(mt_rand(0, 100));
            $usr = new User($userName, $password, 1);
            $this->assertTrue(
                $usr->checkPassword($password),
                'Password verification failed!\n'
            );
        }
    }
}