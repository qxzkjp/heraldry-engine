<?php
declare(strict_types=1);

use Doctrine\ORM\EntityRepository;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Http\Gpc;
use HeraldryEngine\Logger;
use HeraldryEngine\Utility\RandomString;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class LogonControllerTest extends TestCase
{

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function testLoginWithBadPasswordFails(){
        $mockHandler = $this->createMock(\HeraldryEngine\Interfaces\RequestHandlerInterface::class);
        $mockLog = $this->createMock(Logger::class);
        $mockRepo = $this->createMock(EntityRepository::class);
        /*$mockHandler->method('redirect')
            ->willReturn(new RedirectResponse(''));
        $mockHandler->method('redirect')
            ->willReturn(new Response(''));*/
        //$mockHandler->expects($this->once())
         //   ->method('redirect');
        $mockLog->method('getNumFailures')
            ->willReturn(0);
        $mockLog->method('logFailure')
            ->willReturn(true);
        $usr = new User('bob','password', 1);
        $mockRepo->method('findBy')
            ->willReturn([$usr]);
        $clock = new \HeraldryEngine\MockClock();
        $lifetime = new \DateInterval('PT100S');
        $ctx = new \HeraldryEngine\SecurityContext($clock, $lifetime);
        $req = new \Symfony\Component\HttpFoundation\Request(
            [],
            [
                'username' => RandomString::generate(mt_rand(1, 49)),
                'password' => RandomString::generate(mt_rand(1, 49))
            ],
            [],
            [],
            [],
            [
                'HTTP_USER_AGENT' => 'Mozilla/5.0',
                'REMOTE_ADDR' => ''.mt_rand(1, 255).'.'.mt_rand(1, 255).'.'.mt_rand(1, 255).'.'.mt_rand(1, 255)
            ]
        );
        $mockStorage = new MockArraySessionStorage();
        $mockStorage->setSessionData([
            'CSRF' => $ctx->getCSRF()
        ]);
        $session = new \Symfony\Component\HttpFoundation\Session\Session($mockStorage);
        $gpc = new Gpc($ctx);
        $testSubject = new \HeraldryEngine\Controllers\LoginController();
        $result = $testSubject->DoLogin($req, $gpc, $clock, $mockRepo, $session, $ctx, $mockHandler, $mockLog, $lifetime);
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Response::class, $result);
    }
    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function testLoginWithGoodPasswordSucceeds(){
        $mockHandler = $this->createMock(\HeraldryEngine\Interfaces\RequestHandlerInterface::class);
        $mockLog = $this->createMock(Logger::class);
        $mockRepo = $this->createMock(EntityRepository::class);
        $mockHandler->method('redirect')
            ->willReturn(new RedirectResponse('/'));
        $mockHandler->method('handle')
            ->willReturn(new Response(''));
        $mockHandler->expects($this->once())
           ->method('redirect')
            ->with($this->equalTo('/'));
        $mockLog->method('getNumFailures')
            ->willReturn(0);
        $mockLog->method('logFailure')
            ->willReturn(true);
        $userName = RandomString::generate(mt_rand(1, 49));
        $password = RandomString::generate(mt_rand(1, 49));
        $usr = new User($userName,$password, 1);
        $mockRepo->method('findBy')
            ->willReturn([$usr]);
        $clock = new \HeraldryEngine\MockClock();
        $lifetime = new \DateInterval('PT100S');
        $ctx = new \HeraldryEngine\SecurityContext($clock, $lifetime);
        $req = new \Symfony\Component\HttpFoundation\Request(
            [],
            [
                'username' => $userName,
                'password' => $password
            ],
            [],
            [],
            [],
            [
                'HTTP_USER_AGENT' => 'Mozilla/5.0',
                'REMOTE_ADDR' => ''.mt_rand(1, 255).'.'.mt_rand(1, 255).'.'.mt_rand(1, 255).'.'.mt_rand(1, 255)
            ]
        );
        $mockStorage = new MockArraySessionStorage();
        $mockStorage->setSessionData([
            'CSRF' => $ctx->getCSRF()
        ]);
        $session = new \Symfony\Component\HttpFoundation\Session\Session($mockStorage);
        $gpc = new Gpc($ctx);
        $testSubject = new \HeraldryEngine\Controllers\LoginController();
        /** @noinspection PhpParamsInspection */
        $result = $testSubject->DoLogin($req, $gpc, $clock, $mockRepo, $session, $ctx, $mockHandler, $mockLog, $lifetime);
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Response::class, $result);
    }
}