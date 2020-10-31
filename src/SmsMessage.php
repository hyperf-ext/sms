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

use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\Contract\CompressInterface;
use Hyperf\Contract\UnCompressInterface;
use Hyperf\Utils\ApplicationContext;
use HyperfExt\Sms\Contracts\MobileNumberInterface;
use HyperfExt\Sms\Contracts\SenderInterface;
use HyperfExt\Sms\Contracts\SmsManagerInterface;
use HyperfExt\Sms\Contracts\SmsMessageInterface;
use HyperfExt\Sms\Strategies\OrderStrategy;

abstract class SmsMessage implements SmsMessageInterface, CompressInterface, UnCompressInterface
{
    /**
     * @var string
     */
    public $strategy = OrderStrategy::class;

    /**
     * @var string[]
     */
    public $senders;

    /**
     * @var string
     */
    public $sender;

    /**
     * @var string
     */
    public $from;

    /**
     * @var \HyperfExt\Sms\Contracts\MobileNumberInterface
     */
    public $to;

    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    public $template;

    /**
     * @var string
     */
    public $signature;

    /**
     * @var array
     */
    public $data = [];

    public function from(string $from)
    {
        $this->from = $from;

        return $this;
    }

    public function to(MobileNumberInterface $to)
    {
        $this->to = $to;

        return $this;
    }

    public function content(string $content)
    {
        $this->content = $content;

        return $this;
    }

    public function template(string $template)
    {
        $this->template = $template;

        return $this;
    }

    public function signature(string $signature)
    {
        $this->signature = $signature;

        return $this;
    }

    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } elseif (is_string($key)) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    public function sender(string $name)
    {
        $this->sender = $name;

        return $this;
    }

    public function send(?SenderInterface $sender = null): array
    {
        return $sender instanceof SenderInterface
            ? $sender->send($this)
            : ApplicationContext::getContainer()->get(SmsManagerInterface::class)->sendNow($this);
    }

    public function queue(?string $queue = null): bool
    {
        return $this->pushQueuedJob($this->newQueuedJob(), $queue);
    }

    public function later(int $delay, ?string $queue = null): bool
    {
        return $this->pushQueuedJob($this->newQueuedJob(), $queue, $delay);
    }

    /**
     * @return static
     */
    public function uncompress(): CompressInterface
    {
        foreach ($this as $key => $value) {
            if ($value instanceof UnCompressInterface) {
                $this->{$key} = $value->uncompress();
            }
        }

        return $this;
    }

    /**
     * @return static
     */
    public function compress(): UnCompressInterface
    {
        foreach ($this as $key => $value) {
            if ($value instanceof CompressInterface) {
                $this->{$key} = $value->compress();
            }
        }

        return $this;
    }

    /**
     * Push the queued SMS message job onto the queue.
     */
    protected function pushQueuedJob(QueuedSmsMessageJob $job, ?string $queue = null, ?int $delay = null)
    {
        $queue = $queue ?: (property_exists($this, 'queue') ? $this->queue : array_key_first(config('async_queue')));

        return ApplicationContext::getContainer()->get(DriverFactory::class)->get($queue)->push($job, (int) $delay);
    }

    /**
     * Make the queued SMS message job instance.
     */
    protected function newQueuedJob(): QueuedSmsMessageJob
    {
        return new QueuedSmsMessageJob($this);
    }
}
