<?php
namespace Neat\Util;

/**
 * Permission utility.
 */
class Permission
{
    /** @var array */
    private $levels = [];

    /**
     * Constructor.
     *
     * @param array $levels
     */
    public function __construct(array $levels)
    {
        $this->levels = $levels;
    }

    /**
     * Checks permission.
     *
     * @param string $level
     * @param int    $bitmask
     *
     * @return bool
     */
    public function check($level, $bitmask)
    {
        $key = [];
        foreach ($this->levels as $item) $key[$item] = '0';
        $key[$level] = '1';
        $key = implode('', $key);
        $key = bindec($key);

        return (bool)($bitmask & $key);
    }

    /**
     * Converts bitmask to rights.
     *
     * @param int $bitmask
     *
     * @return array
     */
    public function toRights($bitmask)
    {
        $bitmask = decbin($bitmask);
        $diff = count($this->levels) - strlen($bitmask);
        $this->assertBitmaskIsValid($diff, $bitmask);

        $rights = [];
        foreach ($this->levels as $key => $level) {
            $rights[$level] = (bool)$bitmask[$key];
        }

        return $rights;
    }

    /**
     * Converts rights to bitmask.
     *
     * @param array $rights
     *
     * @return int
     */
    public function toBitmask(array $rights)
    {
        $bitmask = [];
        foreach ($this->levels as $level) $bitmask[$level] = '0';

        foreach ($rights as $level => $value) {
            $this->assertLevelExists($level);

            if ($value) $bitmask[$level] = '1';
        }

        $bitmask = implode('', $bitmask);
        $bitmask = bindec($bitmask);

        return $bitmask;
    }

    /**
     * @param int    $diff
     * @param string $bitmask
     * @throws Exception\UnexpectedValueException
     */
    private function assertBitmaskIsValid($diff, $bitmask)
    {
        if (0 != $diff) {
            $msg = sprintf('Invalid bitmask "%s".', $bitmask);
            throw new Exception\UnexpectedValueException($msg);
        }
    }

    /**
     * @param string $level
     * @throws Exception\OutOfBoundsException
     */
    private function assertLevelExists($level)
    {
        if (!in_array($level, $this->levels)) {
            $msg = sprintf('Invalid permission level "%s".', $level);
            throw new Exception\OutOfBoundsException($msg);
        }
    }
}