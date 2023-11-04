<?php
/**
 * @author debuss-a
 */

namespace LaPosteTest;

use InvalidArgumentException;
use LaPoste\Suivi\App;
use LaPoste\Suivi\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    public function testGetLang()
    {
        $request = new Request('123456789123', App::LANG_EN);
        $this->assertEquals(App::LANG_EN, $request->getLang());
    }

    public function testGetId()
    {
        $request = new Request('123456789123');
        $this->assertEquals('123456789123', $request->getId());
    }

    public function testGetIpAddress()
    {
        $request = new Request('123456789123', App::LANG_ES, '192.168.42.21');
        $this->assertEquals('192.168.42.21', $request->getIpAddress());
    }

    public function testInvalidIdThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        new Request('1234567');
    }

    public function testInvalidLangThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        new Request('123456789123', 'fr_CA');
    }

    public function testInvalidIpAddressSetsDefaultOne()
    {
        $request = new Request('123456789123', 'fr_FR', 'not an IP address');
        $this->assertEquals('123.123.123.123', $request->getIpAddress());
    }

    public function testInvalidIpAddressCanSetFromServerRemoteAddr()
    {
        unset($_SERVER['REMOTE_ADDR']);
        $_SERVER['REMOTE_ADDR'] = '192.168.1.42';
        $request = new Request('123456789123', 'fr_FR', 'not an IP address');
        $this->assertEquals('123.123.123.123', $request->getIpAddress());
        unset($_SERVER['REMOTE_ADDR']);
    }
}
