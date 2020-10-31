<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/sms.
 *
 * @link     https://github.com/hyperf-ext/sms
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/sms/blob/master/LICENSE
 */
namespace HyperfExt\Sms\Events;

use HyperfExt\Sms\Contracts\SmsMessageInterface;

class SmsMessageSent
{
    /**
     * The message instance.
     *
     * @var \HyperfExt\Sms\Contracts\SmsMessageInterface
     */
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(SmsMessageInterface $message)
    {
        $this->message = $message;
    }
}
