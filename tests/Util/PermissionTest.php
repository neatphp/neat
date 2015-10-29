<?php
namespace Neat\Test\Util;

use Neat\Util\Permission;

class PermissionTest extends \PHPUnit_Framework_TestCase
{
    /** @var Permission */
    private $subject;

    /** @var array */
    private $rights = [
        'read' => true,
        'write' => false,
        'delete' => false,
    ];

    protected function setUp()
    {
        $this->subject = new Permission(['read', 'write', 'delete']);
    }

    /**
     * @test
     */
    public function check_returnsBool()
    {
        $this->assertTrue($this->subject->check('read', 4));
        $this->assertFalse($this->subject->check('write', 4));
        $this->assertFalse($this->subject->check('delete', 4));
    }

    /**
     * @test
     */
    public function toRights_returnArray()
    {
        $this->assertSame($this->rights, $this->subject->toRights(4));
    }

    /**
     * @test
     */
    public function toBitmask_returnInt()
    {
        $this->assertSame(4, $this->subject->toBitmask($this->rights));
    }

    /**
     * @test
     * @expectedException \Neat\Util\Exception\UnexpectedValueException
     */
    public function toRights_invalidLevel_throwsException()
    {
        $this->subject->toRights(1);
    }

    /**
     * @test
     * @expectedException \Neat\Util\Exception\OutOfBoundsException
     */
    public function toBitmask_invalidLevel_throwsException()
    {
        $rights = $this->rights;
        $rights['test'] = true;
        $this->subject->toBitmask($rights);
    }
}
