<?php

namespace LaPosteTest;

use Laposte\Exception\BadXOkapiKeyException;
use LaPoste\Exception\ResponseDecodeException;
use LaPoste\Suivi\App;
use LaPoste\Suivi\Request;
use LaPoste\Suivi\Response;
use LaPosteTest\Mock\AppMock;
use PHPUnit\Framework\TestCase;
use TypeError;

class AppTest extends TestCase
{

    /** @var string */
    protected $x_okapi_key;

    /** @var AppMock */
    protected $app;

    public function setUp(): void
    {
        $this->x_okapi_key = '1234567891234567891234567891234512345678912345678912345678912345';
        $this->app = new AppMock($this->x_okapi_key);
    }

    public function testMissingXOkapiKeyThrowException()
    {
        $this->expectException(\ArgumentCountError::class);
        new App();
    }

    public function testInvalidXOkapiKeyThrowException()
    {
        $this->expectException(BadXOkapiKeyException::class);
        new App('123456789');
    }

    public function testCallOk()
    {
        $response = $this->app->call(new Request('6A123456789'));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getReturnCode());
    }

    public function testCallInvalidRequest()
    {
        $this->app->setCodeToReturn(400);
        $response = $this->app->call(new Request('6A123456789'));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(400, $response->getReturnCode());
        $this->assertEquals(
            'Votre requête est incorrecte. Veuillez la ressaisir en respectant le format.',
            $response->getReturnMessage()
        );
    }

    public function testCallWithDecodeException()
    {
        $this->expectException(ResponseDecodeException::class);

        $this->app->setCodeToReturn('000');
        $this->app->call(new Request('6A123456789'));
    }

    public function testCallWithTooManyRequestsException()
    {
        $this->expectException(ResponseDecodeException::class);
        $this->expectExceptionCode(429);

        $this->app->setCodeToReturn('429');
        $this->app->call(new Request('6A123456789'));
    }

    public function testCallWithServiceUnavailableException()
    {
        $this->expectException(ResponseDecodeException::class);
        $this->expectExceptionCode(503);

        $this->app->setCodeToReturn('503');
        $this->app->call(new Request('6A123456789'));
    }

    public function testCallWithoutRequestObjectThrowException()
    {
        $this->expectException(TypeError::class);

        $this->app->call('6A123456789');
    }

    public function testCallMultiple()
    {
        $response = $this->app->callMultiple([
            new Request('6A123456789'),
            new Request('6A987654321'),
            new Request('6A147258369')
        ]);

        $this->assertIsArray($response);

        foreach ($response as $item) {
            $this->assertInstanceOf(Response::class, $item);
            $this->assertEquals(200, $item->getReturnCode());
        }
    }
}
