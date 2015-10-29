<?php
namespace Neat\Container\Exception;

/**
 * Container exception.
 */
class ReadonlyException extends \LogicException implements ExceptionInterface {}