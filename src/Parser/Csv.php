<?php
namespace Neat\Parser;

/**
 * CSV parser.
 */
class Csv implements ParserInterface
{
    /** @var string  */
	private $quote = '"';

    /** @var string  */
	private $delimiter = ',';

    /** @var string */
	private $linebreak = "\n";

    /** @var bool|true */
	private $headerEnabled = true;

    /**
     * Constructor.
     *
     * @param string    $quote
     * @param string    $delimiter
     * @param string    $linebreak
     * @param bool|true $headerEnabled
     */
    public function __construct($quote = '"', $delimiter = ',', $linebreak = "\n", $headerEnabled = true)
    {
        $this->quote = $quote;
        $this->delimiter = $delimiter;
        $this->linebreak = $linebreak;
        $this->headerEnabled = $headerEnabled;
    }

	/**
	 * Parses CSV string to array.
	 *
	 * @param string $string
     *
	 * @return array
	 */
	public function parse($string)
	{
		return empty($this->quote) ? $this->parseWithoutQuote($string) : $this->parseWithQuote($string);
	}

	/**
	 * Dumps array to CSV string.
	 *
	 * @param array $array
     *
	 * @return string
	 */
	public function dump(array $array)
	{
        if (empty($array)) return '';

		if ($this->headerEnabled) {
			array_unshift($array, array_keys(reset($array)));
		}

		$string = '';
		foreach ($array as $row) {
			foreach ($row as & $value) {
				$value = str_replace($this->quote, $this->quote . $this->quote, $value);
				$value = $this->quote . $value . $this->quote;
			}

			$string .= implode($this->delimiter, $row) . $this->linebreak;
		}


		return trim($string);
	}

    /**
     * Parses CSV string without quote.
     *
     * @param string $string
     *
     * @return array
     */
    private function parseWithoutQuote($string)
    {
        $array = [];
        $lines = explode($this->linebreak, $string);
        $header = array_shift($lines);
        $fields = explode($this->delimiter, $header);

        foreach ($lines as $line) {
            $values = [];
            foreach (explode($this->delimiter, $line) as $key => $value) {
                $values[$fields[$key]] = $value;
            }

            $array[] = $values;
        }

        return $array;
    }

    /**
     * Parses CSV string with quote.
     *
     * @param string $string
     *
     * @return array
     */
    public function parseWithQuote($string)
    {
        $segment = '';
        $offset = 0;
        $linenum = 1;
        $isQuoted = false;

        $index = 0;
        $columns = [];
        $row = [];
        $array = [];

        $string = trim($string) . $this->linebreak;
        $length = strlen($string);

        for ($i = 0; $i < $length; $i++) {
            $char = $string[$i];
            $offset += 1;

            if ($isQuoted) {
                switch ($char) {
                    case $this->quote:
                        if ($this->quote == $string[$i + 1]) {
                            $segment .= $this->quote;
                            $offset += 1;
                            $i += 1;
                        } else {
                            $isQuoted = false;
                        }

                        break;

                    case $this->delimiter:
                    case $this->linebreak:
                        $this->assertSegmentNotEnclosedInQuotes($linenum, $offset);

                    default:
                        $segment .= $char;
                }
            } else {
                switch ($char) {
                    case $this->quote:
                        $isQuoted = true;

                        break;

                    case $this->delimiter:
                        if (isset($columns[$index])) {
                            $row[$columns[$index]] = $segment;
                        } else {
                            $columns[$index] = $segment;
                        }

                        $segment = '';
                        $index += 1;

                        break;

                    case $this->linebreak:
                        if (isset($columns[$index])) {
                            $row[$columns[$index]] = $segment;
                            $array[] = $row;
                        } else {
                            $columns[$index] = $segment;
                        }

                        $segment = '';
                        $offset = 0;
                        $linenum += 1;

                        $index = 0;
                        $row = [];

                        break;

                    case ' ':
                        break;

                    default:
                        $this->assertSegmentNotEnclosedInQuotes($linenum, $offset);
                }
            }
        }

        return $array;
    }

    /**
     * @param int $linenum
     * @param int $offset
     * @throws Exception\UnexpectedValueException
     */
    private function assertSegmentNotEnclosedInQuotes($linenum, $offset)
    {
        $msg = sprintf('Segment is not enclosed in quotes in line(%s) at position(%s).', $linenum, $offset);
        throw new Exception\UnexpectedValueException($msg);
    }
}