<?php
namespace Neat\Adr;

use Neat\Data\Data;

/**
 * Domain payload.
 *
 * Payload should be used as a data transfer object to send domain-layer results to responder,
 * along with domain status and message indicating the meaning of the domain results.
 */
class Payload extends Data
{
    const ACCEPTED          = 'ACCEPTED';
    const AUTHENTICATED     = 'AUTHENTICATED';
    const AUTHORIZED        = 'AUTHORIZED';
    const CREATED           = 'CREATED';
    const DELETED           = 'DELETED';
    const ERROR             = 'ERROR';
    const FAILURE           = 'FAILURE';
    const FOUND             = 'FOUND';
    const NOT_ACCEPTED      = 'NOT_ACCEPTED';
    const NOT_AUTHENTICATED = 'NOT_AUTHENTICATED';
    const NOT_AUTHORIZED    = 'NOT_AUTHORIZED';
    const NOT_CREATED       = 'NOT_CREATED';
    const NOT_DELETED       = 'NOT_DELETED';
    const NOT_FOUND         = 'NOT_FOUND';
    const NOT_UPDATED       = 'NOT_UPDATED';
    const NOT_VALID         = 'NOT_VALID';
    const PROCESSING        = 'PROCESSING';
    const SUCCESS           = 'SUCCESS';
    const UPDATED           = 'UPDATED';
    const VALID             = 'VALID';

    /** @var string */
    private $status;

    /** @var string */
    private $message;

    /**
     * Retrieves the domain status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the domain status.
     *
     * @param string $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Retrieves the domain message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the domain message.
     *
     * @param string $message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}