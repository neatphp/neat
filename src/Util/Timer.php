<?php
namespace Neat\Util;

/**
 * Timer.
 */
Class Timer
{
	/** @var array */
	private $stack = [];

    /** @var array */
	private $records = [];

	/**
	 * Starts a timer.
	 *
	 * @param string $name
	 */
	public function start($name)
	{
        $this->assertTimerIsNotStarted($name);

		$this->stack[$name] = microtime(true);
	}

	/**
	 * Stops the latest started timer.
     *
     * @return float
	 */
	public function stop()
	{
        $this->assertStackIsNotEmpty();

        $record = microtime(true) - end($this->stack);
        $key = key($this->stack);
        $this->records[$key][] = $record;
        unset($this->stack[$key]);

        return $record;
	}

	/**
	 * Retrieves all records or one record.
	 *
	 * @param string $name
     *
	 * @return array|float
	 */
	public function getRecords($name = null)
	{
		if ($name) {
            $this->assertRecordExists($name);

			return array_sum($this->records[$name]) / count($this->records[$name]);
		} else {
            $records = [];
            foreach ($this->records as $name => $record) $records[$name] = array_sum($record) / count($record);

			return $records;
		}
	}

    /**
     * Retrieves call times of a timer.
     *
     * @param string $name
     *
     * @return int
     */
    public function getCalls($name)
    {
        $this->assertRecordExists($name);

        return count($this->records[$name]);
    }

    /**
     * @param string $name
     * @throws Exception\UnexpectedValueException
     */
    private function assertTimerIsNotStarted($name)
    {
        if (isset($this->stack[$name])) {
            $msg = sprintf('Timer "%s" has already been started.', $name);
            throw new Exception\UnexpectedValueException($msg);
        }
    }

    /**
     * @throws Exception\UnderflowException
     */
    private function assertStackIsNotEmpty()
    {
        if (empty($this->stack)) {
            $msg = 'No timer was started.';
            throw new Exception\UnderflowException($msg);
        }
    }

    /**
     * @param string $name
     * @throws Exception\OutOfBoundsException
     */
    private function assertRecordExists($name)
    {
        if (!isset($this->records[$name])) {
            $msg = sprintf('Record of timer "%s" does not exist.', $name);
            throw new Exception\OutOfBoundsException($msg);
        }
    }
}