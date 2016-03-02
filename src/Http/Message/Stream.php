<?php
namespace Neat\Http\Message;

use Psr\Http\Message\StreamInterface;

/**
 * Data stream.
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
            $stream = fopen($stream, $mode);
        }

        $this->assertStreamIsValid($stream);
        $this->stream = $stream;
        $this->metadata = stream_get_meta_data($stream);
    }

    /**
     * Reads all data from the stream into a string.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $this->rewind();
            $string = $this->getContents();
        } catch (\Exception $e) {
            $string = '';
        }

        return $string;
    }

    /**
     * Closes the stream.
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
     * Detaches the stream.
     *
     * @return resource|null
     */
    public function detach()
    {
        $stream = $this->stream;
        $this->stream   = null;
        $this->metadata = null;

        return $stream;
    }

    /**
     * Retrieves size of the stream.
     *
     * @return int|null
     */
    public function getSize()
    {
        $size = null;

        if ($this->stream) {
            $stats = fstat($this->stream);
            $size  = $stats['size'];
        }

        return $size;
    }

    /**
     * Tells position of the read/write pointer in stream.
     *
     * @return int
     *
     * @throws Exception\RuntimeException
     */
    public function tell()
    {
        $result = ftell($this->stream);

        if (false === $result) {
            $msg = 'Error occurred during telling position of the read/write pointer in stream.';
            throw new Exception\RuntimeException($msg);
        }

        return $result;
    }

    /**
     * Tells whether the read/write pointer is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return !$this->stream || feof($this->stream);
    }

    /**
     * Tells whether the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return $this->metadata && $this->metadata['seekable'];
    }

    /**
     * Seeks to a position in the stream.
     *
     * @param int $offset
     * @param int $whence
     *
     * @throws Exception\RuntimeException
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
     * Seeks to the beginning of the stream.
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * Tells whether the stream is writable.
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
     * Writes data to the stream.
     *
     * @param string $string
     *
     * @return int
     *
     * @throws Exception\RuntimeException
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
     * Tells whether the stream is readable.
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
     * Reads data from the stream.
     *
     * @param int $length
     *
     * @return string
     *
     * @throws Exception\RuntimeException
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
     * Retrieves remaining contents in the stream.
     *
     * @return string
     *
     * @throws Exception\RuntimeException
     */
    public function getContents()
    {
        $this->assertStreamIsAvailable();
        $this->assertStreamIsReadable();

        $result = stream_get_contents($this->stream);

        if (false === $result) {
            $msg = 'Error occurred during getting remaining contents from stream.';
            throw new Exception\RuntimeException($msg);
        }

        return $result;
    }

    /**
     * Retrieves the stream metadata or value of the given key.
     *
     * @param string|null $key
     *
     * @return array|mixed|null
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
     *
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
