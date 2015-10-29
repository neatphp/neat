<?php
namespace Neat\Util;

/**
 * String utility.
 */
class Strings
{
    /**
     * Generates a random password.
     *
     * @param int $length
     *
     * @return string
     */
    public function password($length = 8)
    {
        mt_srand(microtime(true));
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            switch (mt_rand(0, 2)) {
                case 0:
                    $password .= mt_rand(0, 9);
                    break;

                case 1:
                    $password .= chr(mt_rand(97, 122));
                    break;

                case 2:
                    $password .= chr(mt_rand(65, 90));
            }
        }

        return $password;
    }

    /**
     * Censors words within a string
     *
     * @param string       $string
     * @param string|array $words
     * @param string       $replace
     * @param bool         $boundary - tells whether use word boundary to identify word
     *
     * @return string
     */
    public function censor($string, $words, $replace = '***', $boundary = true)
    {
        foreach ((array)$words as $word) {
            $word = preg_quote($word, '/');
            $pattern = $boundary ? '/\b' . $word . '\b/i' : '/' . $word . '/i';
            $string = preg_replace($pattern, $replace, $string);
        }

        return $string;
    }

    /**
     * Highlights words within a string.
     *
     * @param string       $string
     * @param string|array $words
     * @param string       $opening - opening tag
     * @param string       $closing - closing tag
     * @param bool         $boundary - tells whether use word boundary to identify word
     *
     * @return string
     */
    public function highlight($string, $words, $boundary = true, $opening = '<strong>', $closing = '</strong>')
    {
        foreach ((array)$words as $word) {
            $word = preg_quote($word, '/');
            $pattern = $boundary ? '/\b(' . $word . ')\b/i' : '/(' . $word . ')/i';
            $string = preg_replace($pattern, $opening . '\\1' . $closing, $string);
        }

        return $string;
    }

    /**
     * Wraps a string to a given number of characters.
     *
     * @param string $string
     * @param int    $width
     * @param string $break
     *
     * @return string
     */
    public function wordwrap($string, $width = 75, $break = "\n")
    {
        if ($string) {
            $pattern = '/(.{' . $width . '})/u';
            $string = trim(preg_replace($pattern, "\${1}" . $break, $string));
        }

        return $string;
    }

    /**
     * Abbreviates a string to a certain number of characters and preserving words.
     * Abbreviation could be a bit shorter than the number of characters specified.
     *
     * @param string $string
     * @param int    $size
     * @param bool   $strip - tells whether to strip tags
     * @param string $tags - allowed tags
     * @param string $charset
     *
     * @return string
     */
    public function abbreviate($string, $size, $strip = true, $tags = null, $charset = 'utf-8')
    {
        if ($strip) $string = strip_tags($string, $tags);
        $length = strlen($string);
        if ($length <= $size) return $string;

        $string = preg_replace('/[\t\n\r]+/', ' ', $string);
        $string = preg_split('/\s+/', $string);

        $count = 0;
        $words = [];
        foreach ($string as $word) {
            $count += strlen($word);
            if ($count++ > $size) break;

            $words[] = $word;
        }

        return implode(' ', $words) . '...';
    }

    /**
     * Converts string to ASCII.
     *
     * @param string $str
     * @param string $pattern
     *
     * @return string
     */
    public function toASCII($str, $pattern = null)
    {
        if ($pattern) {
            $matches = [];
            preg_match_all($pattern, $str, $matches);

            $search = [];
            $replace = [];
            foreach ((array)$matches[0] as $item) {
                $search[] = $item;
                $replace[] = $this->toASCII($item);
            }

            return str_replace($search, $replace, $str);
        } else {
            $return = '';
            $str = str_split($str);
            foreach ($str as $char) {
                $return .= '&#' . ord($char) . ';';
            }

            return $return;
        }
    }

    /**
     * Finds all E-mail addresses within a string and converts them to ASCII.
     *
     * @param string $string
     *
     * @return string
     */
    public function emailToASCII($string)
    {
        return $this->toASCII($string, '/\w[-._\w]*\w@\w[-._\w]*\w\.\w{2,6}/');
    }

    /**
     * Calculates a math string.
     *
     * @param string $string
     *
     * @return string
     */
    public function calculate($string)
    {
        $calculate = create_function('', 'return (' . $string . ');');

        return $calculate();
    }
}