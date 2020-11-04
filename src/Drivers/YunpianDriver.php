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
 * @see https://www.yunpian.com/doc/zh_CN/intl/single_send.html
 */
class YunpianDriver extends AbstractDriver
{
    protected const ENDPOINT_TEMPLATE = 'https://%s.yunpian.com/%s/%s/%s.%s';

    protected const ENDPOINT_VERSION = 'v2';

    protected const ENDPOINT_FORMAT = 'json';

    public function send(SmsableInterface $smsable): array
    {
        $endpoint = $this->buildEndpoint('sms', 'sms', 'single_send');

        $signature = $smsable->signature ?: $this->config->get('signature', '');

        $content = $smsable->content;

        $response = $this->client->request('post', $endpoint, [
            'form_params' => [
                'apikey' => $this->config->get('api_key'),
                'mobile' => $smsable->to->toE164(),
                'text' => stripos($content, '【') === 0 ? $content : $signature . $content,
            ],
            'exceptions' => false,
        ]);

        $result = $response->toArray();

        if ($result['code']) {
            throw new DriverErrorException($result['msg'], $result['code'], $response);
        }

        return $result;
    }

    protected function buildEndpoint(string $type, string $resource, string $function): string
    {
        return sprintf(self::ENDPOINT_TEMPLATE, $type, self::ENDPOINT_VERSION, $resource, $function, self::ENDPOINT_FORMAT);
    }
}
