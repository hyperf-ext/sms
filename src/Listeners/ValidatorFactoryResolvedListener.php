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
use Hyperf\Validation\Event\ValidatorFactoryResolved;
use HyperfExt\Sms\MobileNumber;
use LogicException;
use Throwable;

class ValidatorFactoryResolvedListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            ValidatorFactoryResolved::class,
        ];
    }

    public function process(object $event)
    {
        /** @var \Hyperf\Validation\Contract\ValidatorFactoryInterface $validatorFactory */
        $validatorFactory = $event->validatorFactory;

        $validatorFactory->extend('mobile_number', function ($attribute, $value, $parameters, $validator) {
            $defaultRegion = count($parameters) <= 1 ? ($parameters[0] ?? config('sms.default_mobile_number_region')) : null;

            try {
                $phoneNumber = new MobileNumber($value, $defaultRegion);
            } catch (Throwable $e) {
                return false;
            }

            $region = $phoneNumber->getRegionCode();

            if ($defaultRegion) {
                return $region === strtoupper($defaultRegion);
            }

            if (empty($parameters) || array_search($region, array_map('strtoupper', $parameters)) !== false) {
                return true;
            }

            return false;
        });

        $validatorFactory->extend('mobile_number_format', function ($attribute, $value, $parameters, $validator) {
            if (empty($parameters)) {
                throw new LogicException();
            }

            $value = (string) $value;
            $defaultRegion = count($parameters) < 2 ? config('sms.default_mobile_number_region') : $parameters[1];

            try {
                $phoneNumber = new MobileNumber($value, $defaultRegion);
            } catch (Throwable $e) {
                return false;
            }

            if ($defaultRegion) {
                return $phoneNumber->getRegionCode() === strtoupper($defaultRegion);
            }

            switch ($format = $parameters[0]) {
                case 'e164':
                    return $phoneNumber->toE164() === $value;
                case 'international':
                    return $phoneNumber->toInternational() === $value;
                case 'national':
                    return $phoneNumber->toNational() === $value;
                case 'rfc3966':
                    return $phoneNumber->toRFC3966() === $value;
                case 'digits':
                    $format = '\d+';
                    break;
            }

            return preg_match('/^' . $format . '$/', $value) > 0;
        });
    }
}
