<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/sms.
 *
 * @link     https://github.com/hyperf-ext/sms
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/sms/blob/master/LICENSE
 */
namespace HyperfExt\Sms\Drivers;

use Hyperf\Logger\LoggerFactory;
use HyperfExt\Sms\Contracts\SmsMessageInterface;
use Psr\Container\ContainerInterface;

class LogDriver extends AbstractDriver
{
    /**
     * The Logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(ContainerInterface $container, array $config)
    {
        parent::__construct($config);

        $this->logger = $container->get(LoggerFactory::class)->get(
            $options['name'] ?? 'sms.local',
            $options['group'] ?? 'default'
        );
    }

    public function send(SmsMessageInterface $message): array
    {
        $log = sprintf(
            "To: %s | Content: \"%s\" | Template: \"%s\" | Data: %s\n",
            $message->to->toE164(),
            $message->content,
            $message->template,
            json_encode($message->data)
        );

        $this->logger->debug($log);

        return compact('status', 'file');
    }
}
