<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/sms.
 *
 * @link     https://github.com/hyperf-ext/sms
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/sms/blob/master/LICENSE
 */
namespace HyperfExt\Sms;

use Hyperf\Utils\ApplicationContext;
use HyperfExt\Contract\HasMobileNumber;
use HyperfExt\Sms\Contracts\SmsManagerInterface;
use HyperfExt\Sms\Contracts\SmsMessageInterface;

class PendingSmsMessage
{
    /**
     * The "to" recipient of the message.
     *
     * @var \HyperfExt\Sms\Contracts\MobileNumberInterface
     */
    protected $to;

    /**
     * @var \HyperfExt\Sms\Contracts\SmsManagerInterface
     */
    protected $manger;

    /**
     * @var \HyperfExt\Sms\Contracts\SenderInterface
     */
    protected $sender;

    public function __construct(SmsManagerInterface $manger)
    {
        $this->manger = $manger;
    }

    /**
     * Set the recipients of the message.
     *
     * @param \HyperfExt\Contract\HasMobileNumber|string $number
     * @param null|int|string $defaultRegion
     *
     * @throws \HyperfExt\Sms\Exceptions\InvalidMobileNumberException
     * @return $this
     */
    public function to($number, $defaultRegion = null)
    {
        $number = $number instanceof HasMobileNumber ? $number->getMobileNumber() : $number;

        $this->to = new MobileNumber($number, $defaultRegion);

        return $this;
    }

    /**
     * Set the sender of the SMS message.
     *
     * @return $this
     */
    public function sender(string $name)
    {
        $this->sender = ApplicationContext::getContainer()->get(SmsManagerInterface::class)->get($name);

        return $this;
    }

    /**
     * Send a new SMS message instance immediately.
     */
    public function sendNow(SmsMessageInterface $message): array
    {
        return $this->manger->sendNow($this->fill($message));
    }

    /**
     * Send a new SMS message instance.
     *
     * @return array|bool
     */
    public function send(SmsMessageInterface $message)
    {
        return $this->manger->send($this->fill($message));
    }

    /**
     * Push the given SMS message onto the queue.
     */
    public function queue(SmsMessageInterface $message, ?string $queue = null): bool
    {
        return $this->manger->queue($this->fill($message), $queue);
    }

    /**
     * Deliver the queued SMS message after the given delay.
     */
    public function later(SmsMessageInterface $message, int $delay, ?string $queue = null): bool
    {
        return $this->manger->later($this->fill($message), $delay, $queue);
    }

    /**
     * Populate the SMS message with the addresses.
     */
    protected function fill(SmsMessageInterface $message): SmsMessageInterface
    {
        $message->to($this->to);
        if ($this->sender) {
            $message->sender($this->sender->getName());
        }
        return $message;
    }
}
