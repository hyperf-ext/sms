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
use HyperfExt\Sms\Contracts\SmsableInterface;
use HyperfExt\Sms\Contracts\SmsManagerInterface;

class PendingSms
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
    public function sendNow(SmsableInterface $smsable): array
    {
        return $this->manger->sendNow($this->fill($smsable));
    }

    /**
     * Send a new SMS message instance.
     *
     * @return array|bool
     */
    public function send(SmsableInterface $smsable)
    {
        return $this->manger->send($this->fill($smsable));
    }

    /**
     * Push the given SMS message onto the queue.
     */
    public function queue(SmsableInterface $smsable, ?string $queue = null): bool
    {
        return $this->manger->queue($this->fill($smsable), $queue);
    }

    /**
     * Deliver the queued SMS message after the given delay.
     */
    public function later(SmsableInterface $smsable, int $delay, ?string $queue = null): bool
    {
        return $this->manger->later($this->fill($smsable), $delay, $queue);
    }

    /**
     * Populate the SMS message with the addresses.
     */
    protected function fill(SmsableInterface $smsable): SmsableInterface
    {
        $smsable->to($this->to);
        if ($this->sender) {
            $smsable->sender($this->sender->getName());
        }
        return $smsable;
    }
}
