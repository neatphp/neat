<?php
namespace Neat\Test\Util;

use Neat\Util\Security;

class SecurityTest extends \PHPUnit_Framework_TestCase
{
    /** @var Security */
    private $mcrpytSecurity;

    /** @var Security */
    private $xorSecurity;

    protected function setUp()
    {
        $this->mcrpytSecurity = new Security;
        $this->xorSecurity = new Security(false);
    }

    /**
     * @test
     */
    public function encryptAndDecrypt_returnsString()
    {
        $key = 'key';
        $string = $this->mcrpytSecurity->encrypt('test', $key);
        $string = $this->mcrpytSecurity->decrypt($string, $key);
        $this->assertSame('test', $string);

        $string = $this->xorSecurity->encrypt('test', $key);
        $string = $this->xorSecurity->decrypt($string, $key);
        $this->assertSame('test', $string);
    }
}
