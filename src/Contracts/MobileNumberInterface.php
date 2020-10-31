<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/sms.
 *
 * @link     https://github.com/hyperf-ext/sms
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/sms/blob/master/LICENSE
 */
namespace HyperfExt\Sms\Contracts;

use Hyperf\Utils\Contracts\Arrayable;

interface MobileNumberInterface extends Arrayable
{
    /**
     * @param string $number number that we are attempting to parse. This can contain formatting
     *                       such as +, ( and -, as well as a phone number extension.
     *
     * @param null|int|string $defaultRegion region that we are expecting the number to be from. This is only used
     *                                       if the number being parsed is not written in international format.
     *                                       The country_code for the number in this case would be stored as that
     *                                       of the default region supplied. If the number is guaranteed to
     *                                       start with a '+' followed by the country calling code, then
     *                                       "ZZ" or null can be supplied.
     *
     * @throws \HyperfExt\Sms\Exceptions\InvalidMobileNumberException
     */
    public function __construct(string $number, $defaultRegion = null);

    public function __toString(): string;

    /**
     * Returns the country code of this phone number.
     */
    public function getCountryCode(): ?int;

    /**
     * Returns the national number of this phone number.
     */
    public function getNationalNumber(): ?string;

    /**
     * Returns the raw input of this phone number.
     */
    public function getRawInput(): string;

    /**
     * Returns the region where a phone number is from. This could be used for geocoding at the region level.
     */
    public function getRegionCode(): string;

    /**
     * Returns the full phone number. The full number is "country code + glue + national number". For example, the glue
     * is one whitespace, then the number of the Google Switzerland office will be written as "41 446681800".
     */
    public function getFullNumber(string $glue = ''): string;

    /**
     * Returns the full phone number with the IDD prefix for out-of-country dialing purposes. If the country calling
     * code is the same as that of the region where the number is from, then NATIONAL formatting will be applied.
     *
     * Note this function takes care of the case for calling inside of NANPA and between Russia and Kazakhstan (who
     * share the same country calling code). In those cases, no international prefix is used. For regions which have
     * multiple international prefixes, the number in its INTERNATIONAL format will be returned instead.
     */
    public function getFullNumberWithIDDPrefix(string $regionCallingFrom, bool $withFormat = false): string;

    /**
     * Formats a phone number in the E.164 format. For example, the number of the Google Switzerland office will be
     * written as "+41446681800".
     */
    public function toE164(): string;

    /**
     * Formats a phone number in the E.164 format without plus sign prefix. For example, the number of the Google
     * Switzerland office will be written as "41446681800".
     */
    public function toE164WithoutLeadingPlus(): string;

    /**
     * Formats the phone number in the international format. Formats are consistent with the definition in ITU-T
     * Recommendation E123. For example, the number of the Google Switzerland office will be written as "044 668 1800".
     */
    public function toInternational(): string;

    /**
     * Formats a phone number in the national format. Formats are consistent with the definition in ITU-T Recommendation
     * E123. For example, the number of the Google Switzerland office will be written as "044 668 1800".
     */
    public function toNational(): string;

    /**
     * Formats a phone number in the RFC3966 format. For example, the number of the Google Switzerland office will be
     * written as "tel:+41-44-668-1800".
     */
    public function toRFC3966(): string;

    public function toArray(): array;
}
