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

use HyperfExt\Sms\Commands\GenSmsCommand;
use HyperfExt\Sms\Contracts\SmsManagerInterface;
use HyperfExt\Sms\Listeners\ValidatorFactoryResolvedListener;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                SmsManagerInterface::class => SmsManager::class,
            ],
            'commands' => [
                GenSmsCommand::class,
            ],
            'listeners' => [
                ValidatorFactoryResolvedListener::class,
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for HyperfExt\\SMS.',
                    'source' => __DIR__ . '/../publish/sms.php',
                    'destination' => BASE_PATH . '/config/autoload/sms.php',
                ],
            ],
        ];
    }
}
