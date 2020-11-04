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
 * @see http://www.yuntongxun.com/doc/rest/sms/3_2_2_2.html
 */
class RonglianDriver extends AbstractDriver
{
    protected const ENDPOINT_TEMPLATE = 'https://%s:%s/%s/%s/%s/%s/%s?sig=%s';

    protected const SERVER_IP = 'app.cloopen.com';

    protected const DEBUG_SERVER_IP = 'sandboxapp.cloopen.com';

    protected const DEBUG_TEMPLATE_ID = 1;

    protected const SERVER_PORT = '8883';

    protected const SDK_VERSION = '2013-12-26';

    protected const SUCCESS_CODE = '000000';

    public function send(SmsableInterface $smsable): array
    {
        $datetime = date('YmdHis');

        $endpoint = $this->buildEndpoint('SMS', 'TemplateSMS', $datetime);

        $response = $this->client->request('post', $endpoint, [
            'json' => [
                'to' => $smsable->to->getNationalNumber(),
                'templateId' => (int) ($this->config->get('debug') ? self::DEBUG_TEMPLATE_ID : $smsable->template),
                'appId' => $this->config->get('app_id'),
                'datas' => $smsable->data,
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=utf-8',
                'Authorization' => base64_encode($this->config->get('account_sid') . ':' . $datetime),
            ],
        ]);

        $result = $response->toArray();

        if ($result['statusCode'] != self::SUCCESS_CODE) {
            throw new DriverErrorException($result['statusCode'], $result['statusCode'], $response);
        }

        return $result;
    }

    protected function buildEndpoint(string $type, string $resource, string $datetime): string
    {
        $serverIp = $this->config->get('debug') ? self::DEBUG_SERVER_IP : self::SERVER_IP;

        $accountType = $this->config->get('is_sub_account') ? 'SubAccounts' : 'Accounts';

        $sig = strtoupper(md5($this->config->get('account_sid') . $this->config->get('account_token') . $datetime));

        return sprintf(self::ENDPOINT_TEMPLATE, $serverIp, self::SERVER_PORT, self::SDK_VERSION, $accountType, $this->config->get('account_sid'), $type, $resource, $sig);
    }
}
