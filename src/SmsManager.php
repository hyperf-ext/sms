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
use HyperfExt\Sms\Contracts\SmsManagerInterface;
use HyperfExt\Sms\Contracts\SmsMessageInterface;
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
        $this->config = $container->get(ConfigInterface::class);
    }

    public function get(string $name): SenderInterface
    {
        if (empty($this->senders[$name])) {
            $this->senders[$name] = $this->resolve($name);
        }

        return $this->senders[$name];
    }

    public function sendNow(SmsMessageInterface $message): array
    {
        $senders = empty($message->sender) ? $this->applyStrategy($message) : [$message->sender];

        $exception = null;

        foreach ($senders as $sender) {
            try {
                return $message->send($this->get($sender));
            } catch (Throwable $throwable) {
                $exception = empty($exception)
                    ? new StrategicallySendMessageException('The SMS manger encountered some errors on strategically send the message', $throwable)
                    : $exception->appendStack($exception);
            }
        }

        throw $exception;
    }

    public function send(SmsMessageInterface $message)
    {
        if ($message instanceof ShouldQueue) {
            return $message->queue();
        }

        return $this->sendNow($message);
    }

    public function queue(SmsMessageInterface $message, ?string $queue = null): bool
    {
        return $message->queue($queue);
    }

    public function later(SmsMessageInterface $message, int $delay, ?string $queue = null): bool
    {
        return $message->later($delay, $queue);
    }

    /**
     * @param \HyperfExt\Contract\HasMobileNumber|string $number
     * @param null|int|string $defaultRegion
     *
     * @throws \HyperfExt\Sms\Exceptions\InvalidMobileNumberException
     */
    public function to($number, $defaultRegion = null): PendingSmsMessage
    {
        return (new PendingSmsMessage($this))->to($number, $defaultRegion);
    }

    protected function applyStrategy(SmsMessageInterface $message): array
    {
        $senders = (is_array($message->senders) && count($message->senders) > 0)
            ? $message->senders
            : (
                is_array($this->config['default']['senders'])
                    ? $this->config['default']['senders']
                    : [$this->config['default']['senders']]
            );

        if (empty($senders)) {
            throw new LogicException('The SMS senders value is missing on SmsMessage class or default config');
        }

        $strategy = $message->strategy ?: $this->config['default']['strategy'];

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
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("The SMS sender [{$name}] is not defined.");
        }

        return make(Sender::class, compact('name', 'config'));
    }

    /**
     * Get the mail connection configuration.
     *
     * @return array
     */
    protected function getConfig(string $name)
    {
        return $this->config->get("sms.senders.{$name}");
    }
}
