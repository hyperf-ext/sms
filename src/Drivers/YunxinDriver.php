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

use GuzzleHttp\Exception\ClientException;
use HyperfExt\Sms\Contracts\SmsMessageInterface;
use HyperfExt\Sms\Exceptions\DriverErrorException;

/**
 * @see https://dev.yunxin.163.com/docs/product/%E7%9F%AD%E4%BF%A1/%E7%9F%AD%E4%BF%A1%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97
 */
class YunxinDriver extends AbstractDriver
{
    protected const ENDPOINT_TEMPLATE = 'https://api.netease.im/%s/%s.action';

    protected const ENDPOINT_ACTION = 'sendCode';

    protected const SUCCESS_CODE = 200;

    public function send(SmsMessageInterface $message): array
    {
        $data = $message->data;

        $action = isset($data['action']) ? $data['action'] : self::ENDPOINT_ACTION;

        $endpoint = $this->buildEndpoint('sms', $action);

        switch ($action) {
            case 'sendCode':
                $params = $this->buildSendCodeParams($message);

                break;
            case 'verifyCode':
                $params = $this->buildVerifyCodeParams($message);

                break;
            default:
                throw new DriverErrorException(sprintf('action: %s not supported', $action), 0);
        }

        $headers = $this->buildHeaders();

        try {
            $response = $this->client->post($endpoint, $params, $headers);

            $result = $response->toArray();

            if (! isset($result['code']) || $result['code'] !== self::SUCCESS_CODE) {
                $code = isset($result['code']) ? $result['code'] : 0;
                $error = isset($result['msg']) ? $result['msg'] : json_encode($result, JSON_UNESCAPED_UNICODE);

                throw new DriverErrorException($error, $code, $response);
            }
        } catch (ClientException $e) {
            throw new DriverErrorException($e->getMessage(), $e->getCode(), $e->getResponse(), $e);
        }

        return $result;
    }

    public function buildSendCodeParams(SmsMessageInterface $message): array
    {
        $data = $message->data;
        $template = $message->template;

        return [
            'mobile' => $message->to->toE164(),
            'authCode' => array_key_exists('code', $data) ? $data['code'] : '',
            'deviceId' => array_key_exists('device_id', $data) ? $data['device_id'] : '',
            'templateid' => is_string($template) ? $template : '',
            'codeLen' => $this->config->get('code_length', 4),
            'needUp' => $this->config->get('need_up', false),
        ];
    }

    public function buildVerifyCodeParams(SmsMessageInterface $message): array
    {
        $data = $message->data;

        if (! array_key_exists('code', $data)) {
            throw new DriverErrorException('"code" cannot be empty', 0);
        }

        return [
            'mobile' => $message->to->toE164(),
            'code' => $data['code'],
        ];
    }

    protected function buildEndpoint(string $resource, string $function): string
    {
        return sprintf(self::ENDPOINT_TEMPLATE, $resource, strtolower($function));
    }

    protected function buildHeaders(): array
    {
        $headers = [
            'AppKey' => $this->config->get('app_key'),
            'Nonce' => md5(uniqid('easysms')),
            'CurTime' => (string) time(),
            'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8',
        ];

        $headers['CheckSum'] = sha1("{$this->config->get('app_secret')}{$headers['Nonce']}{$headers['CurTime']}");

        return $headers;
    }
}
