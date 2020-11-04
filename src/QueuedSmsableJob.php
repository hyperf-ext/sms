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

use Hyperf\AsyncQueue\Job;
use HyperfExt\Sms\Contracts\SmsableInterface;

class QueuedSmsableJob extends Job
{
    /**
     * @var \HyperfExt\Sms\Contracts\SmsableInterface
     */
    public $smsable;

    public function __construct(SmsableInterface $smsable)
    {
        $this->smsable = $smsable;
    }

    public function handle()
    {
        $this->smsable->send();
    }
}
