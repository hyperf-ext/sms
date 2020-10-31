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

interface SmsManagerInterface
{
    /**
     * Send the given message immediately.
     *
     * @throws \HyperfExt\Sms\Exceptions\StrategicallySendMessageException
     */
    public function sendNow(SmsMessageInterface $message): array;

    /**
     * Send the given message.
     *
     * @return array|bool
     */
    public function send(SmsMessageInterface $message);

    /**
     * Queue the message for sending.
     */
    public function queue(SmsMessageInterface $message, ?string $queue = null): bool;

    /**
     * Deliver the queued message after the given delay.
     */
    public function later(SmsMessageInterface $message, int $delay, ?string $queue = null): bool;
}
