<?php
namespace Neat\Parser;

/**
 * Json parser.
 */
class Json implements ParserInterface
{
	/**
	 * Parses string to array.
	 *
	 * @param string $string
	 *
	 * @return array
	 */
	public function parse($string)
	{
        return json_decode($string, true);
	}

	/**
	 * Dumps array to string.
	 *
	 * @param array $array
	 *
	 * @return string
	 */
	public function dump(array $array)
    {
        return json_encode($array);
    }

    /**
     * Retrieves error.
     *
     * @return string
     */
    public function getError()
    {
        return json_last_error_msg();
    }
}