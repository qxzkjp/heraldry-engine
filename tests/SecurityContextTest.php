<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

final class SecurityContextTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSettingASecurityContextFromASessionObjectWorks()
    {
        for($i=0; $i<50; ++$i){
            $clock = new \HeraldryEngine\MockClock(1111);
            $id = mt_rand();
            $accessLevel = mt_rand();
            $browser = \HeraldryEngine\Utility\RandomString::generate(mt_rand(0,100));
            $OS = \HeraldryEngine\Utility\RandomString::generate(mt_rand(0,100));
            $ip = '' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255);
            $mockStorage = new MockArraySessionStorage($id);
            $session = new \Symfony\Component\HttpFoundation\Session\Session($mockStorage);
            $session->start();
            $session->set('userID', $id);
            $session->set('accessLevel', $accessLevel);
            $session->set('userIP', $ip);
            $session->set('OS', $OS);
            $session->set('browser', $browser);
            /**
             * @var \HeraldryEngine\SecurityContext
             */
            $ctx = new \HeraldryEngine\SecurityContext($clock, new DateInterval("PT100S"), $session);
            $this->assertEquals(
                $id,
                $ctx->GetUserID(),
                'User ID did not match'
            );
            $this->assertEquals(
                $accessLevel,
                $ctx->GetAccessLevel(),
                'User access level did not match'
            );
            $this->assertEquals(
                $ip,
                $ctx->getUserIP(),
                'User IP did not match'
            );
            $this->assertEquals(
                $browser,
                $ctx->getBrowser(),
                'Browser did not match'
            );
            $this->assertEquals(
                $OS,
                $ctx->getOS(),
                'OS did not match'
            );
        }
    }

    /**
     * @throws Exception
     */
    public function testSettingASecurityContextFromAnArrayWorks()
    {
        for($i=0; $i<50; ++$i){
            $clock = new \HeraldryEngine\MockClock(1111);
            $id = mt_rand();
            $accessLevel = mt_rand();
            $browser = \HeraldryEngine\Utility\RandomString::generate(mt_rand(0,100));
            $OS = \HeraldryEngine\Utility\RandomString::generate(mt_rand(0,100));
            $ip = '' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255);
            /**
             * @var \HeraldryEngine\SecurityContext
             */
            $ctx = new \HeraldryEngine\SecurityContext($clock, new DateInterval("PT100S"), [
                'userID' => $id,
                'accessLevel' => $accessLevel,
                'browser' => $browser,
                'OS' => $OS,
                'userIP' => $ip
            ]);
            $this->assertEquals(
                $id,
                $ctx->GetUserID(),
                'User ID did not match'
            );
            $this->assertEquals(
                $accessLevel,
                $ctx->GetAccessLevel(),
                'User access level did not match'
            );
            $this->assertEquals(
                $ip,
                $ctx->getUserIP(),
                'User IP did not match'
            );
            $this->assertEquals(
                $browser,
                $ctx->getBrowser(),
                'Browser did not match'
            );
            $this->assertEquals(
                $OS,
                $ctx->getOS(),
                'OS did not match'
            );
        }
    }

    /**
     * @throws Exception
     */
    public function testSavingASecurityContextToASessionObjectWorks()
    {
        for($i=0; $i<50; ++$i){
            $clock = new \HeraldryEngine\MockClock(1111);
            $id = mt_rand();
            $accessLevel = mt_rand();
            $browser = \HeraldryEngine\Utility\RandomString::generate(mt_rand(0,100));
            $OS = \HeraldryEngine\Utility\RandomString::generate(mt_rand(0,100));
            $ip = '' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255);
            /**
             * @var \HeraldryEngine\SecurityContext
             */
            $ctx = new \HeraldryEngine\SecurityContext($clock, new DateInterval("PT100S"), [
                'userID' => $id,
                'accessLevel' => $accessLevel,
                'browser' => $browser,
                'OS' => $OS,
                'userIP' => $ip
            ]);
            
            $mockStorage = new MockArraySessionStorage($id);
            $session = new \Symfony\Component\HttpFoundation\Session\Session($mockStorage);
            $session->start();
            $ctx->StoreContext($session);

            $this->assertEquals(
                $id,
                $session->get('userID'),
                'User ID did not match'
            );
            $this->assertEquals(
                $accessLevel,
                $session->get('accessLevel'),
                'User access level did not match'
            );
            $this->assertEquals(
                $ip,
                $session->get('userIP'),
                'User IP did not match'
            );
            $this->assertEquals(
                $browser,
                $session->get('browser'),
                'Browser did not match'
            );
            $this->assertEquals(
                $OS,
                $session->get('OS'),
                'OS did not match'
            );
        }
    }

    /**
     * @throws Exception
     */
    public function testSecurityContextExpiresOneSecondAfterLifetime()
    {
        for ($i = 0; $i < 50; ++$i) {
            $clock = new \HeraldryEngine\MockClock(1111);
            $lifetime = mt_rand();
            $id = mt_rand();
            $accessLevel = mt_rand();
            $browser = \HeraldryEngine\Utility\RandomString::generate(mt_rand(0, 100));
            $OS = \HeraldryEngine\Utility\RandomString::generate(mt_rand(0, 100));
            $ip = '' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255);
            /**
             * @var \HeraldryEngine\SecurityContext
             */
            $ctx = new \HeraldryEngine\SecurityContext($clock, new DateInterval("PT" . $lifetime . "S"), [
                'userID' => $id,
                'accessLevel' => $accessLevel,
                'browser' => $browser,
                'OS' => $OS,
                'userIP' => $ip
            ]);
            $clock->addTime($lifetime + 1 );
            $this->assertEquals(
                0,
                $ctx->GetUserID(),
                'User ID did not match'
            );
            $this->assertEquals(
                2,
                $ctx->GetAccessLevel(),
                'User access level did not match'
            );
            $this->assertEquals(
                '',
                $ctx->getUserIP(),
                'User IP did not match'
            );
            $this->assertEquals(
                '',
                $ctx->getBrowser(),
                'Browser did not match'
            );
            $this->assertEquals(
                '',
                $ctx->getOS(),
                'OS did not match'
            );
        }
    }
    /**
     * @throws Exception
     */
    public function testSecurityContextExpiryWorksARandomAmountOfTimeAfterLifetime()
    {
        for ($i = 0; $i < 50; ++$i) {
            $timestamp = time();
            $clock = new \HeraldryEngine\MockClock($timestamp);
            $lifetime = mt_rand();
            $extraTime = mt_rand(1, PHP_INT_MAX - $lifetime);
            $id = mt_rand();
            $accessLevel = mt_rand();
            $browser = \HeraldryEngine\Utility\RandomString::generate(mt_rand(0, 100));
            $OS = \HeraldryEngine\Utility\RandomString::generate(mt_rand(0, 100));
            $ip = '' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255);
            /**
             * @var \HeraldryEngine\SecurityContext
             */
            $ctx = new \HeraldryEngine\SecurityContext($clock, new DateInterval("PT" . $lifetime . "S"), [
                'userID' => $id,
                'accessLevel' => $accessLevel,
                'browser' => $browser,
                'OS' => $OS,
                'userIP' => $ip
            ]);
            $clock->addTime($lifetime + $extraTime);
            $this->assertEquals(
                true,
                $ctx->isExpired(),
                'Security context not reporting that it is expired'
            );
            $this->assertEquals(
                0,
                $ctx->GetUserID(),
                "User ID did not match expired value"
            );
            $this->assertEquals(
                2,
                $ctx->GetAccessLevel(),
                'User access level did not match expired value'
            );
            $this->assertEquals(
                '',
                $ctx->getUserIP(),
                'User IP did not match expired value'
            );
            $this->assertEquals(
                '',
                $ctx->getBrowser(),
                'Browser did not match expired value'
            );
            $this->assertEquals(
                '',
                $ctx->getOS(),
                'OS did not match expired value'
            );
        }
    }
}