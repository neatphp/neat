<?php
namespace Neat\Parser;

/**
 * Parser.
 */
interface ParserInterface
{
	/**
	 * Parses string to array.
	 *
	 * @param string $string
	 *
	 * @return array
	 */
	public function parse($string);

	/**
	 * Dumps array to string.
	 *
	 * @param array $array
	 *
	 * @return string
	 */
	public function dump(array $array);
}