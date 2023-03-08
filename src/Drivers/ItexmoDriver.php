<?php


namespace HyperfExt\Sms\Drivers;


use HyperfExt\Sms\Contracts\SmsableInterface;
use HyperfExt\Sms\Exceptions\DriverErrorException;
use HyperfExt\Sms\Exceptions\RequestException;

class ItexmoDriver extends AbstractDriver
{
    /**
     * 验证码 - 即时发送
     */
    protected const SERVER = 'https://api.itexmo.com/api/broadcast-otp';

    public function __construct(array $config)
    {
        if (empty($config['email']) || empty($config['password']) || empty($config['api_code']) || empty($config['sender_id'])) {
            throw new \InvalidArgumentException('SMS 配置信息不全！');
        }
        parent::__construct($config);
    }

    public function send(SmsableInterface $smsable): array
    {
        if (empty($smsable->to)) {
            throw new \InvalidArgumentException('Empty phone number');
        }

        if (empty($smsable->content)) {
            throw new \InvalidArgumentException('Empty message');
        }

        $recipient = $smsable->to->getCountryCode() === 63 ? $smsable->to->getNationalNumber() : $smsable->to->getFullNumberWithIDDPrefix('PH');
        $data = [
            'Email' => $this->config->get('email'),
            'Password' => $this->config->get('password'),
            'Recipients' => [$recipient],
            'Message' => $smsable->content,
            'ApiCode' => $this->config->get('api_code'),
            'SenderId' => $this->config->get('sender_id'),
        ];

        try {
            $response = $this->client->postJson(self::SERVER, $data);
            $result = $response->toArray();

            if ($result['Error'] != false) {
                throw new DriverErrorException($result['Message'], $result['Code'] ?? -1, $response);
            }

            return $result;
        } catch (RequestException $e) {
            $response = $e->getResponse();

            return $response->toArray();
        } catch (\Throwable $e) {
            throw new DriverErrorException($e->getMessage(), $e->getCode());
        }
    }
}