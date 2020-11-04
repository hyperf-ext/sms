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

use HyperfExt\Sms\Contracts\SmsableInterface;
use HyperfExt\Sms\Exceptions\DriverErrorException;

/**
 * @see https://www.juhe.cn/docs/api/id/54
 */
class JuheDataDriver extends AbstractDriver
{
    protected const ENDPOINT_URL = 'http://v.juhe.cn/sms/send';

    protected const ENDPOINT_FORMAT = 'json';

    public function send(SmsableInterface $smsable): array
    {
        $params = [
            'mobile' => $smsable->to->getNationalNumber(),
            'tpl_id' => $smsable->template,
            'tpl_value' => $this->formatTemplateVars($smsable->data),
            'dtype' => self::ENDPOINT_FORMAT,
            'key' => $this->config->get('app_key'),
        ];

        $response = $this->client->get(self::ENDPOINT_URL, $params);

        $result = $response->toArray();

        if ($result['error_code']) {
            throw new DriverErrorException($result['reason'], $result['error_code'], $response);
        }

        return $result;
    }

    protected function formatTemplateVars(array $vars): string
    {
        $formatted = [];

        foreach ($vars as $key => $value) {
            $formatted[sprintf('#%s#', trim($key, '#'))] = $value;
        }

        return http_build_query($formatted);
    }
}
