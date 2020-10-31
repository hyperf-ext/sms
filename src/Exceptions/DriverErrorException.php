<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/sms.
 *
 * @link     https://github.com/hyperf-ext/sms
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/sms/blob/master/LICENSE
 */
namespace HyperfExt\Sms\Exceptions;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class DriverErrorException extends RuntimeException
{
    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    public $response;

    public function __construct(string $message, $code = null, ResponseInterface $response = null, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->response = $response;
    }

    final public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
