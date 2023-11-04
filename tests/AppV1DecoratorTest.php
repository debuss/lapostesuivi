<?php
/**
 * @author debuss-a
 */

namespace LaPosteTest;

use LaPoste\Exception\BadXOkapiKeyException;
use LaPoste\Exception\ResponseDecodeException;
use LaPoste\Suivi\App;
use LaPoste\Suivi\AppV1Decorator;
use LaPoste\Suivi\Request;
use LaPosteTest\Mock\AppMock;
use PHPUnit\Framework\TestCase;
use TypeError;

class AppV1DecoratorTest extends TestCase
{

    /** @var string */
    protected $x_okapi_key;

    /** @var AppV1Decorator */
    protected $app;

    /** @var AppMock */
    protected $mock;

    public function setUp(): void
    {
        $this->x_okapi_key = '1234567891234567891234567891234512345678912345678912345678912345';
        $this->app = new AppV1Decorator(new AppMock($this->x_okapi_key));
        $this->mock = new AppMock($this->x_okapi_key);
    }

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
        $response = $this->app->call(new Request('6A18987970674'));

        $this->assertIsArray($response);
        $this->assertEquals('6A18987970674', $response['code']);
        $this->assertEquals('04/07/2020', $response['date']);
        $this->assertCount(6, $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('link', $response);
        $this->assertArrayHasKey('type', $response);
    }

    public function testCallBadRequest()
    {
        $this->mock->setCodeToReturn(400);

        $app = new AppV1Decorator($this->mock);
        $response = $app->call(new Request('6A123456789'));

        $this->assertIsArray($response);
        $this->assertCount(2, $response);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('BAD_REQUEST', $response['code']);
    }

    public function testCallAuthorizedRequest()
    {
        $this->mock->setCodeToReturn(401);

        $app = new AppV1Decorator($this->mock);
        $response = $app->call(new Request('6A123456789'));

        $this->assertIsArray($response);
        $this->assertCount(2, $response);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('UNAUTHORIZED', $response['code']);
    }

    public function testCallResourceNotFoundRequest()
    {
        $this->mock->setCodeToReturn(404);

        $app = new AppV1Decorator($this->mock);
        $response = $app->call(new Request('6A123456789'));

        $this->assertIsArray($response);
        $this->assertCount(2, $response);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('RESOURCE_NOT_FOUND', $response['code']);
    }

    public function testCallInternalServerErrorRequest()
    {
        $this->mock->setCodeToReturn(500);

        $app = new AppV1Decorator($this->mock);
        $response = $app->call(new Request('6A123456789'));

        $this->assertIsArray($response);
        $this->assertCount(2, $response);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('INTERNAL_SERVER_ERROR', $response['code']);
    }

    public function testCallGatewayTimeoutRequest()
    {
        $this->mock->setCodeToReturn(504);

        $app = new AppV1Decorator($this->mock);
        $response = $app->call(new Request('6A123456789'));

        $this->assertIsArray($response);
        $this->assertCount(2, $response);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('GATEWAY_TIMEOUT', $response['code']);
    }

    public function testCallWithDecodeException()
    {
        $this->expectException(ResponseDecodeException::class);

        $this->mock->setCodeToReturn('000');

        $app = new AppV1Decorator($this->mock);
        $app->call(new Request('6A123456789'));
    }

    public function testCallWithoutRequestObjectThrowException()
    {
        $this->expectException(TypeError::class);

        $this->app->call('6A123456789');
    }

    public function testCallMultiple()
    {
        $responses = $this->app->callMultiple([
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
