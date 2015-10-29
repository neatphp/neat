<?php
namespace Neat\Container\Exception;

/**
 * Container exception.
 */
class CircularDependencyException extends \LogicException implements ExceptionInterface {}