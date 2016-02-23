<?php
namespace Neat\Http\Message;

use Psr\Http\Message\StreamInterface;

/**
 * Stream.
 */
class Stream implements StreamInterface
{
    /** @var resource */
    private $stream;

    /** @var array */
    private $metadata;

    /**
     * Constructor.
     *
     * @param string|resource $stream
     * @param string          $mode
     */
    public function __construct($stream, $mode = 'r+')
    {
        if (is_string($stream)) {
            $this->stream = fopen($stream, $mode);
        } else {
            $this->assertStreamIsValid($stream);
            $this->stream = $stream;
        }

        $this->metadata = stream_get_meta_data($stream);
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        try {
            $this->rewind();
            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        $stream = $this->detach();
        if ($stream) {
            fclose($stream);
        }
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $stream = $this->stream;
        $this->stream = null;
        $this->metadata = null;

        return $stream;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        $size = null;
        if ($this->stream) {
            $stats = fstat($this->stream);
            $size = $stats['size'];
        }

        return $size;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        $result = ftell($this->stream);
        if (false === $result) {
            $msg = 'Error occurred during telling position of pointer in stream.';
            throw new Exception\RuntimeException($msg);
        }

        return $result;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return !$this->stream || feof($this->stream);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return $this->metadata && $this->metadata['seekable'];
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $this->assertStreamIsAvailable();
        $this->assertStreamIsSeekable();

        $result = fseek($this->stream, $offset, $whence);
        if (-1 === $result) {
            $msg = 'Error occurred during seeking to a position in stream.';
            throw new Exception\RuntimeException($msg);
        }
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        if (!$this->metadata) {
            return false;
        }

        $mode = $this->metadata['mode'];
        return (
            strstr($mode, 'x')
            || strstr($mode, 'w')
            || strstr($mode, 'c')
            || strstr($mode, 'a')
            || strstr($mode, '+')
        );
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        $this->assertStreamIsAvailable();
        $this->assertStreamIsWritable();

        $result = fwrite($this->stream, $string);
        if (false === $result) {
            $msg = 'Error occurred during writing data to stream.';
            throw new Exception\RuntimeException($msg);
        }

        return $result;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        if (!$this->metadata) {
            return false;
        }

        $mode = $this->metadata['mode'];
        return (
            strstr($mode, 'r')
            || strstr($mode, '+')
        );
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        $this->assertStreamIsAvailable();
        $this->assertStreamIsReadable();

        $result = fread($this->stream, $length);
        if (false === $result) {
            $msg = 'Error occurred during reading data from stream.';
            throw new Exception\RuntimeException($msg);
        }

        return $result;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        $this->assertStreamIsAvailable();
        $this->assertStreamIsReadable();

        $result = stream_get_contents($this->stream);
        if (false === $result) {
            $msg = 'Error occurred during getting remaining content from stream.';
            throw new Exception\RuntimeException($msg);
        }

        return $result;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        if ($key) {
            return isset($this->metadata[$key]) ? $this->metadata[$key] : null;
        }

        return $this->metadata;
    }

    /**
     * @param mixed $stream
     * @throws Exception\InvalidArgumentException
     */
    private function assertStreamIsValid($stream)
    {
        if (!is_resource($stream) || 'stream' !== get_resource_type($stream)) {
            $msg = 'Stream should be a string stream identifier or stream resource.';
            throw new Exception\InvalidArgumentException($msg);
        }
    }

    /**
     * @throws Exception\RuntimeException
     */
    private function assertStreamIsAvailable()
    {
        if (!$this->stream) {
            $msg = 'No stream available.';
            throw new Exception\RuntimeException($msg);
        }
    }

    /**
     * @throws Exception\RuntimeException
     */
    private function assertStreamIsSeekable()
    {
        if (!$this->isSeekable()) {
            $msg = 'Stream is not seekable';
            throw new Exception\RuntimeException($msg);
        }
    }

    /**
     * @throws Exception\RuntimeException
     */
    private function assertStreamIsReadable()
    {
        if (!$this->isReadable()) {
            $msg = 'Stream is not readable.';
            throw new Exception\RuntimeException($msg);
        }
    }

    /**
     * @throws Exception\RuntimeException
     */
    private function assertStreamIsWritable()
    {
        if (!$this->isWritable()) {
            $msg = 'Stream is not writable.';
            throw new Exception\RuntimeException($msg);
        }
    }
}
