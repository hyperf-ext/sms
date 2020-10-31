<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/sms.
 *
 * @link     https://github.com/hyperf-ext/sms
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/sms/blob/master/LICENSE
 */
namespace HyperfExt\Sms\Contracts;

interface DriverInterface
{
    /**
     * Send the message.
     *
     * @throws \HyperfExt\Sms\Exceptions\DriverErrorException
     */
    public function send(SmsMessageInterface $message): array;
}
