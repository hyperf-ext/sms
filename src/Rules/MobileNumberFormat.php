<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/sms.
 *
 * @link     https://github.com/hyperf-ext/sms
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/sms/blob/master/LICENSE
 */
namespace HyperfExt\Sms\Rules;

class MobileNumberFormat
{
    /**
     * @var string
     */
    protected $format;

    /**
     * @var null|string
     */
    protected $defaultRegion;

    /**
     * Create a new in rule instance.
     */
    public function __construct(string $format, ?string $defaultRegion = null)
    {
        $this->format = $format;
        $this->defaultRegion = $defaultRegion;
    }

    /**
     * Convert the rule to a validation string.
     *
     * @see \Hyperf\Validation\ValidationRuleParser::parseParameters
     */
    public function __toString(): string
    {
        return 'phone_number_format:' . $this->format . ($this->defaultRegion ?: '');
    }
}
