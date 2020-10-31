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

use HyperfExt\Sms\Contracts\MobileNumberInterface;
use HyperfExt\Sms\Exceptions\InvalidMobileNumberException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;

class MobileNumber implements MobileNumberInterface
{
    /**
     * @var \libphonenumber\PhoneNumberUtil
     */
    protected $util;

    /**
     * @var \libphonenumber\PhoneNumber
     */
    protected $info;

    public function __construct(string $number, $defaultRegion = null)
    {
        $this->util = PhoneNumberUtil::getInstance();

        $defaultRegion = $defaultRegion ?: config('sms.default_mobile_number_region');

        if (preg_match('/^\d+$/', (string) $defaultRegion) > 0) {
            $number = '+' . $defaultRegion . $number;
            $defaultRegion = null;
        }

        try {
            $this->info = $this->util->parse($number, $defaultRegion);
        } catch (NumberParseException $e) {
        }

        if (empty($this->info) || $this->util->getNumberType($this->info) !== PhoneNumberType::MOBILE) {
            throw new InvalidMobileNumberException('The value supplied did not seem to be a mobile number.');
        }
    }

    public function __toString(): string
    {
        return $this->toE164();
    }

    public function getCountryCode(): ?int
    {
        return $this->info->{__FUNCTION__}();
    }

    public function getNationalNumber(): ?string
    {
        return $this->info->{__FUNCTION__}();
    }

    public function getRawInput(): string
    {
        return $this->info->{__FUNCTION__}();
    }

    public function getRegionCode(): string
    {
        return $this->util->getRegionCodeForNumber($this->info);
    }

    public function getFullNumber(string $glue = ''): string
    {
        return $this->getCountryCode() . $glue . $this->getNationalNumber();
    }

    public function getFullNumberWithIDDPrefix(string $regionCallingFrom, bool $withFormat = false): string
    {
        $number = $this->util->formatOutOfCountryCallingNumber($this->info, $regionCallingFrom);
        return $withFormat ? $number : str_replace(['-', ' ', '(', ')'], '', $number);
    }

    public function toE164(): string
    {
        return $this->util->format($this->info, PhoneNumberFormat::E164);
    }

    public function toE164WithoutLeadingPlus(): string
    {
        return substr($this->toE164(), 1);
    }

    public function toInternational(): string
    {
        return $this->util->format($this->info, PhoneNumberFormat::INTERNATIONAL);
    }

    public function toNational(): string
    {
        return $this->util->format($this->info, PhoneNumberFormat::NATIONAL);
    }

    public function toRFC3966(): string
    {
        return $this->util->format($this->info, PhoneNumberFormat::RFC3966);
    }

    public function toArray(): array
    {
        return [
            'countryCode' => $this->getCountryCode(),
            'nationalNumber' => $this->getNationalNumber(),
            'regionCode' => $this->getRegionCode(),
        ];
    }
}
