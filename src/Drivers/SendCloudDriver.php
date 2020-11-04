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
 * @see http://sendcloud.sohu.com/doc/sms/
 */
class SendCloudDriver extends AbstractDriver
{
    const ENDPOINT_TEMPLATE = 'http://www.sendcloud.net/smsapi/%s';

    public function send(SmsableInterface $smsable): array
    {
        $params = [
            'smsUser' => $this->config->get('sms_user'),
            'templateId' => $smsable->template,
            'msgType' => $smsable->to->getCountryCode() === 86 ? 0 : 2,
            'phone' => $smsable->to->getFullNumberWithIDDPrefix('CN'),
            'vars' => $this->formatTemplateVars($smsable->data),
        ];

        if ($this->config->get('timestamp', false)) {
            $params['timestamp'] = time() * 1000;
        }

        $params['signature'] = $this->generateSign($params, $this->config->get('sms_key'));

        $response = $this->client->post(sprintf(self::ENDPOINT_TEMPLATE, 'send'), $params);

        $result = $response->toArray();

        if (! $result['result']) {
            throw new DriverErrorException($result['message'], $result['statusCode'], $response);
        }

        return $result;
    }

    protected function formatTemplateVars(array $vars): string
    {
        $formatted = [];

        foreach ($vars as $key => $value) {
            $formatted[sprintf('%%%s%%', trim($key, '%'))] = $value;
        }

        return json_encode($formatted, JSON_FORCE_OBJECT);
    }

    protected function generateSign(array $params, string $key): string
    {
        ksort($params);

        return md5(sprintf('%s&%s&%s', $key, urldecode(http_build_query($params)), $key));
    }
}
