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
 * @see http://www.smsbao.com/openapi/
 */
class SmsBaoDriver extends AbstractDriver
{
    protected const ENDPOINT_URL = 'http://api.smsbao.com/%s';

    protected const SUCCESS_CODE = '0';

    protected $errorStatuses = [
        '30' => '密码错误',
        '40' => '账号不存在',
        '41' => '余额不足',
        '42' => '帐户已过期',
        '43' => 'IP地址限制',
        '50' => '内容含有敏感词',
        '51' => '手机号码不正确',
    ];

    public function send(SmsableInterface $smsable): array
    {
        $data = $smsable->content;

        if ($smsable->to->getCountryCode() === 86) {
            $number = $smsable->to->getNationalNumber();
            $action = 'sms';
        } else {
            $number = $smsable->to->toE164();
            $action = 'wsms';
        }

        $params = [
            'u' => $this->config->get('user'),
            'p' => md5($this->config->get('password')),
            'm' => $number,
            'c' => $data,
        ];

        $response = $this->client->get($this->buildEndpoint($action), $params);

        $result = $response->toArray();

        if (reset($result) !== self::SUCCESS_CODE) {
            throw new DriverErrorException($this->errorStatuses[$result[0]], $result[0], $response);
        }

        return $result;
    }

    protected function buildEndpoint($type)
    {
        return sprintf(self::ENDPOINT_URL, $type);
    }
}
