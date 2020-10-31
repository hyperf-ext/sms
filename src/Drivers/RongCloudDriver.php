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
 * @see http://www.rongcloud.cn/docs/sms_service.html#send_sms_code
 */
class RongCloudDriver extends AbstractDriver
{
    protected const ENDPOINT_TEMPLATE = 'http://api.sms.ronghub.com/%s.%s';

    protected const ENDPOINT_ACTION = 'sendCode';

    protected const ENDPOINT_FORMAT = 'json';

    protected const ENDPOINT_REGION = '86';  // 中国区，目前只支持此国别

    protected const SUCCESS_CODE = 200;

    public function send(SmsMessageInterface $message): array
    {
        $data = $message->data;
        $action = array_key_exists('action', $data) ? $data['action'] : self::ENDPOINT_ACTION;
        $endpoint = $this->buildEndpoint($action);

        $headers = [
            'Nonce' => uniqid(),
            'App-Key' => $this->config->get('app_key'),
            'Timestamp' => time(),
        ];
        $headers['Signature'] = $this->generateSign($headers);

        switch ($action) {
            case 'sendCode':
                $params = [
                    'mobile' => $message->to->getNationalNumber(),
                    'region' => self::ENDPOINT_REGION,
                    'templateId' => $message->template,
                ];
                break;
            case 'verifyCode':
                if (! array_key_exists('code', $data) or ! array_key_exists('sessionId', $data)) {
                    throw new DriverErrorException('"code" or "sessionId" is not set', 0);
                }
                $params = [
                    'code' => $data['code'],
                    'sessionId' => $data['sessionId'],
                ];
                break;
            case 'sendNotify':
                $params = [
                    'mobile' => $message->to->getNationalNumber(),
                    'region' => self::ENDPOINT_REGION,
                    'templateId' => $message->template,
                ];
                $params = array_merge($params, $data);
                break;
            default:
                throw new DriverErrorException(sprintf('action: %s not supported', $action));
        }

        try {
            $response = $this->client->post($endpoint, $params, $headers);

            $result = $response->toArray();

            if ($result['code'] !== self::SUCCESS_CODE) {
                throw new DriverErrorException($result['errorMessage'], $result['code'], $response);
            }
        } catch (ClientException $e) {
            throw new DriverErrorException($e->getMessage(), $e->getCode(), $e->getResponse(), $e);
        }

        return $result;
    }

    protected function generateSign(array $params): string
    {
        return sha1(sprintf('%s%s%s', $this->config->get('app_secret'), $params['Nonce'], $params['Timestamp']));
    }

    protected function buildEndpoint(string $action): string
    {
        return sprintf(self::ENDPOINT_TEMPLATE, $action, self::ENDPOINT_FORMAT);
    }
}
