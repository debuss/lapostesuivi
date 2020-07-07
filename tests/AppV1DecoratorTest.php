<?php
/**
 * @author debuss-a
 */

namespace LaPoste\Suivi;

use LaPoste\Exception\BadXOkapiKeyException;
use LaPoste\Exception\ResponseDecodeException;
use LaPosteTest\Mock\AppMock;
use PHPUnit\Framework\TestCase;
use TypeError;

class AppV1DecoratorTest extends TestCase
{

    public function testMissingXOkapiKeyThrowException()
    {
        $this->expectException(\ArgumentCountError::class);
        new AppV1Decorator(new App());
    }

    public function testInvalidXOkapiKeyThrowException()
    {
        $this->expectException(BadXOkapiKeyException::class);
        new AppV1Decorator(new App('123456789'));
    }

    public function testCallOk()
    {
        $x_okapi_key = '1234567891234567891234567891234512345678912345678912345678912345';
        $app = new AppV1Decorator(new AppMock($x_okapi_key));
        $response = $app->call(new Request('6A18987970674'));

        $this->assertIsArray($response);
        $this->assertEquals('6A18987970674', $response['code']);
        $this->assertEquals('04/07/2020', $response['date']);
        $this->assertCount(6, $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('link', $response);
        $this->assertArrayHasKey('type', $response);
    }

    public function testCallInvalidRequest()
    {
        $x_okapi_key = '1234567891234567891234567891234512345678912345678912345678912345';
        $mock = new AppMock($x_okapi_key);
        $mock->setCodeToReturn(400);

        $app = new AppV1Decorator($mock);
        $response = $app->call(new Request('6A123456789'));

        $this->assertIsArray($response);
        $this->assertCount(2, $response);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('BAD_REQUEST', $response['code']);
    }

    public function testCallWithDecodeException()
    {
        $this->expectException(ResponseDecodeException::class);

        $x_okapi_key = '1234567891234567891234567891234512345678912345678912345678912345';
        $mock = new AppMock($x_okapi_key);
        $mock->setCodeToReturn('000');

        $app = new AppV1Decorator($mock);
        $app->call(new Request('6A123456789'));
    }

    public function testCallWithoutRequestObjectThrowException()
    {
        $this->expectException(TypeError::class);

        $x_okapi_key = '1234567891234567891234567891234512345678912345678912345678912345';
        $app = new AppV1Decorator(new AppMock($x_okapi_key));
        $app->call('6A123456789');
    }

    public function testCallMultiple()
    {
        $x_okapi_key = '1234567891234567891234567891234512345678912345678912345678912345';
        $app = new AppV1Decorator(new AppMock($x_okapi_key));

        $responses = $app->callMultiple([
            new Request('6A123456789'),
            new Request('6A987654321'),
            new Request('6A147258369')
        ]);

        $this->assertIsArray($responses);

        foreach ($responses as $response) {
            $this->assertArrayHasKey('data', $response);

            $response = $response['data'];

            $this->assertIsArray($response);
            $this->assertEquals('6A18987970674', $response['code']);
            $this->assertEquals('04/07/2020', $response['date']);
            $this->assertCount(6, $response);
            $this->assertArrayHasKey('status', $response);
            $this->assertArrayHasKey('message', $response);
            $this->assertArrayHasKey('link', $response);
            $this->assertArrayHasKey('type', $response);
        }
    }
}
