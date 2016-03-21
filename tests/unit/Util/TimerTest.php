<?php
namespace Neat\Test\Util;

use Neat\Util\Timer;

class TimerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Timer */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Timer;
        $this->subject->start('Timer1');
        $this->subject->start('Timer2');
        $this->subject->start('Timer3');
    }

    /**
     * @test
     */
    public function start_nonExistingTimer()
    {
        $this->subject->start('Timer4');
    }

    /**
     * @test
     * @expectedException \Neat\Util\Exception\UnexpectedValueException
     */
    public function start_existingTimer_throwsException()
    {
        $this->subject->start('Timer1');
    }

    /**
     * @test
     */
    public function stop_timerExists()
    {
        $this->assertInternalType('float', $this->subject->stop());
    }

    /**
     * @test
     * @expectedException \Neat\Util\Exception\UnderflowException
     */
    public function stop_noTimerExists_throwsException()
    {
        $this->subject->stop();
        $this->subject->stop();
        $this->subject->stop();
        $this->subject->stop();
    }

    /**
     * @test
     */
    public function getRecords_recordExists()
    {
        $this->subject->stop();
        $this->assertInternalType('float', $this->subject->getRecords('Timer3'));
        $this->subject->stop();
        $this->assertInternalType('float', $this->subject->getRecords('Timer2'));
        $this->subject->stop();
        $this->assertInternalType('float', $this->subject->getRecords('Timer1'));

        $this->assertInternalType('array', $this->subject->getRecords());
        $this->assertSame(3, count($this->subject->getRecords()));
    }

    /**
     * @test
     * @expectedException \Neat\Util\Exception\OutOfBoundsException
     */
    public function getRecords_noRecordExists()
    {
        $this->subject->stop();
        $this->subject->getRecords('Timer1');
    }

    /**
     * @test
     */
    public function getCalls_recordExists()
    {
        $this->subject->stop();
        $this->assertSame(1, $this->subject->getCalls('Timer3'));

        $this->subject->start('Timer3');
        $this->subject->stop();
        $this->assertSame(2, $this->subject->getCalls('Timer3'));
    }

    /**
     * @test
     * @expectedException \Neat\Util\Exception\OutOfBoundsException
     */
    public function getCalls_noRecordExists()
    {
        $this->subject->stop();
        $this->subject->getCalls('Timer1');
    }
}
