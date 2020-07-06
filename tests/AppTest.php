<?php

namespace LaPosteTest;

require_once __DIR__.'/../vendor/autoload.php';

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
        $x_okapi_key = '1234567891234567891234567891234512345678912345678912345678912345';
        $app = new AppMock($x_okapi_key);
        $response = $app->call(new Request('6A123456789'));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getReturnCode());
    }

    public function testCallInvalidRequest()
    {
        $x_okapi_key = '1234567891234567891234567891234512345678912345678912345678912345';
        $app = new AppMock($x_okapi_key);
        $app->setCodeToReturn(400);
        $response = $app->call(new Request('6A123456789'));

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

        $x_okapi_key = '1234567891234567891234567891234512345678912345678912345678912345';
        $app = new AppMock($x_okapi_key);
        $app->setCodeToReturn('000');
        $app->call(new Request('6A123456789'));
    }

    public function testCallWithoutRequestObjectThrowException()
    {
        $this->expectException(TypeError::class);

        $x_okapi_key = '1234567891234567891234567891234512345678912345678912345678912345';
        $app = new AppMock($x_okapi_key);
        $app->call('6A123456789');
    }

    public function testCallMultiple()
    {
        $x_okapi_key = '1234567891234567891234567891234512345678912345678912345678912345';
        $app = new AppMock($x_okapi_key);
        $response = $app->callMultiple([
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
