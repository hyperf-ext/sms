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

/**
 * @property string[]                    $senders
 * @property string                      $strategy
 * @property null|string                 $from
 * @property \HyperfExt\Sms\Contracts\MobileNumberInterface $to
 * @property null|string                 $content
 * @property null|string                 $template
 * @property null|string                 $signature
 * @property array                       $data
 */
interface SmsMessageInterface
{
    /**
     * Set the SMS message sender number.
     *
     * @return $this
     */
    public function from(string $from);

    /**
     * Set the SMS message recipient number.
     *
     * @return $this
     */
    public function to(MobileNumberInterface $to);

    /**
     * Set the SMS message content.
     *
     * @return $this
     */
    public function content(string $content);

    /**
     * Set the SMS message template.
     *
     * @return $this
     */
    public function template(string $template);

    /**
     * Set the SMS message signature.
     *
     * @return $this
     */
    public function signature(string $signature);

    /**
     * Set the SMS message data.
     *
     * @param array|string $key
     * @param null|mixed $value
     *
     * @return $this
     */
    public function with($key, $value = null);

    /**
     * Set the sender name of the SMS message.
     *
     * @return $this
     */
    public function sender(string $name);

    /**
     * Send the SMS message immediately.
     *
     * @throws \HyperfExt\Sms\Exceptions\DriverErrorException
     */
    public function send(?SenderInterface $sender = null): array;

    /**
     * Queue the SMS message for sending.
     */
    public function queue(?string $queue = null): bool;

    /**
     * Deliver the queued SMS message after the given delay.
     */
    public function later(int $delay, ?string $queue = null): bool;
}
