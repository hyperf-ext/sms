<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/sms.
 *
 * @link     https://github.com/hyperf-ext/sms
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/sms/blob/master/LICENSE
 */
namespace HyperfExt\Sms\Strategies;

use HyperfExt\Sms\Concerns\HasSenderFilter;
use HyperfExt\Sms\Contracts\MobileNumberInterface;
use HyperfExt\Sms\Contracts\StrategyInterface;

class OrderStrategy implements StrategyInterface
{
    use HasSenderFilter;

    public function apply(array $senders, MobileNumberInterface $number): array
    {
        return array_values($this->filterSenders($senders, $number));
    }
}
