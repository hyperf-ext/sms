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

use RuntimeException;
use Throwable;

class StrategicallySendMessageException extends RuntimeException
{
    protected $stack = [];

    public function __construct($message, Throwable $throwable)
    {
        parent::__construct($message, 0);

        $this->appendStack($throwable);
    }

    public function appendStack(Throwable $throwable)
    {
        $this->stack[] = $throwable;
    }

    /**
     * @return \Throwable[]
     */
    public function getStack()
    {
        return $this->stack;
    }
}
