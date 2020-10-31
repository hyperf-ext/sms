<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/sms.
 *
 * @link     https://github.com/hyperf-ext/sms
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/sms/blob/master/LICENSE
 */
namespace HyperfExt\Sms\Listeners;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Hyperf\Validation\Rule;
use HyperfExt\Sms\Rules\MobileNumber;
use HyperfExt\Sms\Rules\MobileNumberFormat;

class BootApplicationListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event)
    {
        if (! Rule::hasMacro('mobileNumber')) {
            Rule::macro('mobileNumber', function ($regionCodes, string ...$_) {
                return new MobileNumber($regionCodes, ...$_);
            });
        }

        if (! Rule::hasMacro('mobileNumberFormat')) {
            Rule::macro('mobileNumberFormat', function (string $format, ?string $defaultRegion = null) {
                return new MobileNumberFormat($format, $defaultRegion);
            });
        }
    }
}
