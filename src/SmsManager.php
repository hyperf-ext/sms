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

use Hyperf\Contract\ConfigInterface;
use HyperfExt\Contract\ShouldQueue;
use HyperfExt\Sms\Contracts\SenderInterface;
use HyperfExt\Sms\Contracts\SmsableInterface;
use HyperfExt\Sms\Contracts\SmsManagerInterface;
use HyperfExt\Sms\Exceptions\StrategicallySendMessageException;
use InvalidArgumentException;
use LogicException;
use Psr\Container\ContainerInterface;
use Throwable;

class SmsManager implements SmsManagerInterface
{
    /**
     * The container instance.
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * The array of resolved senders.
     *
     * @var \HyperfExt\Sms\Contracts\SenderInterface[]
     */
    protected $senders = [];

    /**
     * The config instance.
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new Mail manager instance.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $container->get(ConfigInterface::class)->get('sms');
    }

    public function get(string $name): SenderInterface
    {
        if (empty($this->senders[$name])) {
            $this->senders[$name] = $this->resolve($name);
        }

        return $this->senders[$name];
    }

    public function sendNow(SmsableInterface $smsable): array
    {
        $senders = empty($smsable->sender) ? $this->applyStrategy($smsable) : [$smsable->sender];

        $exception = null;

        foreach ($senders as $sender) {
            try {
                return $smsable->send($this->get($sender));
            } catch (Throwable $throwable) {
                $exception = empty($exception)
                    ? new StrategicallySendMessageException('The SMS manger encountered some errors on strategically send the message', $throwable)
                    : $exception->appendStack($exception);
            }
        }

        throw $exception;
    }

    public function send(SmsableInterface $smsable)
    {
        if ($smsable instanceof ShouldQueue) {
            return $smsable->queue();
        }

        return $this->sendNow($smsable);
    }

    public function queue(SmsableInterface $smsable, ?string $queue = null): bool
    {
        return $smsable->queue($queue);
    }

    public function later(SmsableInterface $smsable, int $delay, ?string $queue = null): bool
    {
        return $smsable->later($delay, $queue);
    }

    /**
     * @param \HyperfExt\Contract\HasMobileNumber|string $number
     * @param null|int|string $defaultRegion
     *
     * @throws \HyperfExt\Sms\Exceptions\InvalidMobileNumberException
     */
    public function to($number, $defaultRegion = null): PendingSms
    {
        return (new PendingSms($this))->to($number, $defaultRegion);
    }

    protected function applyStrategy(SmsableInterface $smsable): array
    {
        $senders = (is_array($smsable->senders) && count($smsable->senders) > 0)
            ? $smsable->senders
            : (
                is_array($this->config['default']['senders'])
                    ? $this->config['default']['senders']
                    : [$this->config['default']['senders']]
            );

        if (empty($senders)) {
            throw new LogicException('The SMS senders value is missing on SmsMessage class or default config');
        }

        $strategy = $smsable->strategy ?: $this->config['default']['strategy'];

        if (empty($strategy)) {
            throw new LogicException('The SMS strategy value is missing on SmsMessage class or default config');
        }

        return make($strategy)->apply($senders);
    }

    /**
     * Resolve the given sender.
     */
    protected function resolve(string $name): SenderInterface
    {
        $config = $this->config['senders'][$name] ?? null;

        if (is_null($config)) {
            throw new InvalidArgumentException("The SMS sender [{$name}] is not defined.");
        }

        return make(Sender::class, compact('name', 'config'));
    }
}
