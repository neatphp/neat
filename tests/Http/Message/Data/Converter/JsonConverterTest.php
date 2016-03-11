<?php
namespace Converter;

/**
 * Class JsonConverterTest
 *
 * @group Cable
 * @group DataConverter
 */
class JsonConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Class under test

     */
    protected $converter;

    /**
     * Set up
     *
     * @return void
     */
    public function setUp()
    {
        $this->converter = $this->getMock(
            array('getLastError', 'getLastErrorMessage', 'jsonEncode', 'jsonDecode')
        );
    }

    /**
     * Tear down
     *
     * @return void
     */
    public function tearDown()
    {
        $this->converter = null;
    }

    /**
     * Data provider for json error codes
     *
     * @return array
     */
    public function getJsonErrorCodeData()
    {
        $cases = array();

        #0 case: Syntax error
        $cases[] = array(JSON_ERROR_SYNTAX);

        #1 case: Control char error
        $cases[] = array(JSON_ERROR_CTRL_CHAR);

        #2 case: Depth error
        $cases[] = array(JSON_ERROR_DEPTH);

        #3 case: State mismatch error
        $cases[] = array(JSON_ERROR_STATE_MISMATCH);

        #4 case: UTF-8 error
        $cases[] = array(JSON_ERROR_UTF8);

        if (version_compare(phpversion(), '5.5.0', '>=')) {
            #5 case: Infinity or not a number error
            $cases[] = array(JSON_ERROR_INF_OR_NAN);

            #6 case: Recursion error
            $cases[] = array(JSON_ERROR_RECURSION);

            #7 case: Unsupported type error
            $cases[] = array(JSON_ERROR_UNSUPPORTED_TYPE);
        }

        return $cases;
    }

    /**
     * Data provider for testIsEncoded
     *
     * @return array
     */
    public function getTestIsEncodedData()
    {
        $cases = array();

        #0 case: Check json-string
        $cases[] = array('{data: "data"}', JSON_ERROR_NONE, true);

        #1 case: Check object
        $cases[] = array(new \stdClass(), JSON_ERROR_SYNTAX, false);

        #2 case: Check integer
        $cases[] = array(123, JSON_ERROR_SYNTAX, false);

        #3 case: Check string
        $cases[] = array('a string', JSON_ERROR_SYNTAX, false);

        #4 case: Check float
        $cases[] = array(1.01, JSON_ERROR_SYNTAX, false);

        #5 case: Check boolean
        $cases[] = array(false, JSON_ERROR_SYNTAX, false);

        return $cases;
    }

    /**
     * testIsEncoded
     *
     * @param mixed   $data          Data to check
     * @param integer $lastJsonError Last json error
     * @param boolean $expected      Expected (is encoded) result
     *
     * @dataProvider getTestIsEncodedData
     */
    public function testIsEncoded($data, $lastJsonError, $expected)
    {
        // Assertions
        $this->converter->expects($this->once())
            ->method('jsonDecode')
            ->with($this->equalTo($data));

        $this->converter->expects($this->once())
            ->method('getLastError')
            ->willReturn($lastJsonError);

        // Run test
        $isEncoded = $this->converter->isEncoded($data);

        $this->assertEquals($expected, $isEncoded);
    }

    /**
     * Test encode will throw exception if an encoding error occurs
     *
     * @param integer $errorCode Error code
     *
     * @return void
     *
     * @dataProvider getJsonErrorCodeData
     */
    public function testEncodeWillThrowExceptionIfAnEncodingErrorOccurs($errorCode)
    {
        // Set Assertion

        // Prepare test
        $this->converter->expects($this->once())
            ->method('getLastError')
            ->willReturn($errorCode);

        // Run test
        $this->converter->encode('{data}');
    }

    /**
     * Test encode returns encoded string
     *
     * @return void
     */
    public function testEncodeReturnsEncodedStringStream()
    {
        $data = array('data' => 'data');
        $expectedJson = '{data: "data"}';

        // Prepare test
        $this->converter->expects($this->once())
            ->method('jsonEncode')
            ->with($this->equalTo($data))
            ->willReturn($expectedJson);

        $this->converter->expects($this->once())
            ->method('getLastError')
            ->willReturn(JSON_ERROR_NONE);

        // Run test
        $stream = $this->converter->encode($data);

        // Assertions
        $this->assertSame($expectedJson, (string)$stream);
    }

    /**
     * Test encode will throw exception if an encoding error occurs
     *
     * @param integer $errorCode Error code
     *
     * @return void
     *
     * @dataProvider getJsonErrorCodeData
     */
    public function testDecodeWillThrowExceptionIfAnDecodingErrorOccurs($errorCode)
    {
        // Set Assertion

        // Prepare test
        $this->converter->expects($this->once())
            ->method('getLastError')
            ->willReturn($errorCode);

        // Run test
        $this->converter->decode('{data}');
    }

    /**
     * Test encode returns encoded string
     *
     * @return void
     */
    public function testDecodeReturnsEncodedString()
    {
        $expectedData = array('data' => 'data');
        $json = '{data: "data"}';

        // Prepare test
        $this->converter->expects($this->once())
            ->method('jsonDecode')
            ->with($this->equalTo($json))
            ->willReturn($expectedData);

        $this->converter->expects($this->once())
            ->method('getLastError')
            ->willReturn(JSON_ERROR_NONE);

        // Run test
        $data = $this->converter->decode($json);

        // Assertions
        $this->assertEquals($expectedData, $data);
    }
}
