<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/sms.
 *
 * @link     https://github.com/hyperf-ext/sms
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/sms/blob/master/LICENSE
 */
namespace HyperfExt\Sms\Concerns;

use HyperfExt\Sms\Contracts\MobileNumberInterface;

trait HasSenderFilter
{
    protected function filterSenders(array $senders, MobileNumberInterface $number): array
    {
        $region = strtolower($number->getRegionCode());
        $output = [];
        foreach ($senders as $key => $value) {
            if (is_array($value)) {
                if (in_array($region, array_map('strtolower', $value))) {
                    $output[] = $key;
                }
            } else {
                $output[] = $value;
            }
        }
        return $output;
    }
}
