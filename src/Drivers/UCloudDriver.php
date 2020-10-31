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

use HyperfExt\Sms\Contracts\SmsMessageInterface;
use HyperfExt\Sms\Exceptions\DriverErrorException;

class UCloudDriver extends AbstractDriver
{
    protected const ENDPOINT_URL = 'https://api.ucloud.cn';

    protected const ENDPOINT_Action = 'SendUSMSMessage';

    protected const SUCCESS_CODE = 0;

    public function send(SmsMessageInterface $message): array
    {
        $params = $this->buildParams($message);

        $response = $this->client->get(self::ENDPOINT_URL, $params);

        $result = $response->toArray();

        if ($result['RetCode'] != self::SUCCESS_CODE) {
            throw new DriverErrorException($result['Message'], $result['RetCode'], $response);
        }

        return $result;
    }

    protected function buildParams(SmsMessageInterface $message): array
    {
        $data = $message->data;
        $params = [
            'Action' => self::ENDPOINT_Action,
            'SigContent' => $message->signature ?: $this->config->get('sig_content'),
            'TemplateId' => $message->template,
            'PublicKey' => $this->config->get('public_key'),
        ];
        $code = isset($data['code']) ? $data['code'] : '';
        if (is_array($code) && ! empty($code)) {
            foreach ($code as $key => $value) {
                $params['TemplateParams.' . $key] = $value;
            }
        } else {
            if (! empty($code) || ! is_null($code)) {
                $params['TemplateParams.0'] = $code;
            }
        }

        $mobiles = isset($data['mobiles']) ? $data['mobiles'] : '';
        if (! empty($mobiles)) {
            if (is_array($mobiles)) {
                foreach ($mobiles as $key => $value) {
                    $params['PhoneNumbers.' . $key] = $value;
                }
            } else {
                $params['PhoneNumbers.0'] = $mobiles;
            }
        } else {
            $params['PhoneNumbers.0'] = $message->to->getNationalNumber();
        }

        if (! is_null($this->config->get('project_id')) && ! empty($this->config->get('project_id'))) {
            $params['ProjectId'] = $this->config->get('project_id');
        }

        $signature = $this->generateSign($params, $this->config->get('private_key'));
        $params['Signature'] = $signature;

        return $params;
    }

    protected function generateSign(array $params, string $privateKey): string
    {
        ksort($params);

        $paramsData = '';
        foreach ($params as $key => $value) {
            $paramsData .= $key;
            $paramsData .= $value;
        }
        $paramsData .= $privateKey;

        return sha1($paramsData);
    }
}
