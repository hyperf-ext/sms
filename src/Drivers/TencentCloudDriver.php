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

use Hyperf\Utils\Arr;
use HyperfExt\Sms\Contracts\SmsableInterface;
use HyperfExt\Sms\Exceptions\DriverErrorException;

/**
 * @see https://cloud.tencent.com/document/product/382/38763
 */
class TencentCloudDriver extends AbstractDriver
{
    protected const ENDPOINT_HOST = 'sms.tencentcloudapi.com';

    protected const ENDPOINT_METHOD = 'POST';

    protected const ENDPOINT_CONTENT_TYPE = 'application/json';

    protected const SERVICE = 'sms';

    protected const ACTION = 'SendSms';

    protected const HASH_ALGORITHM = 'sha256';

    protected const SIGN_ALGORITHM = 'TC3-HMAC-SHA256';

    protected const SIGN_END_DELIMITER = 'tc3_request';

    public function send(SmsableInterface $smsable): array
    {
        $timestamp = time();

        $headers = [
            'Host' => self::ENDPOINT_HOST,
            'Content-Type' => self::ENDPOINT_CONTENT_TYPE,
            'X-TC-Action' => self::ACTION,
            'X-TC-Timestamp' => $timestamp,
            'X-TC-Version' => '2019-07-11',
        ];

        $params = [
            'PhoneNumberSet' => [$smsable->to->toE164()],
            'TemplateID' => $smsable->template,
            'SmsSdkAppid' => $this->config->get('sdk_app_id'),
            'TemplateParamSet' => empty($smsable->data) ? null : $smsable->data,
        ];

        if ($smsable->to->getCountryCode() === 86) {
            $params['Sign'] = $smsable->signature ?: $this->config->get('sign');
        } else {
            $params['SenderId'] = $this->config->get('from' . ($smsable->from ?: 'default'));
        }

        $params = array_filter($params);

        $headers['Authorization'] = $this->buildAuthorization($headers, $params, $timestamp);

        $response = $this->client->request(self::ENDPOINT_METHOD, $this->buildEndpointUrl(), [
            'headers' => $headers,
            'body' => json_encode($params),
        ]);

        $result = $response->toArray();

        $status = Arr::get($result, 'Response.SendStatusSet.0');

        if ($status['Code'] != 'Ok') {
            throw new DriverErrorException($status['Message'], $status['Code'], $response);
        }

        return $result;
    }

    protected function buildEndpointUrl()
    {
        return 'https://' . self::ENDPOINT_HOST;
    }

    protected function buildAuthorization(array $headers, array $params, int $timestamp)
    {
        ksort($headers);

        $date = gmdate('Y-m-d', $timestamp);

        $canonicalHeaders = strtolower(implode(
            "\n",
            array_map(function ($v, $k) {
                return $k . ':' . $v;
            }, $headers, array_keys($headers))
        )) . "\n";

        $signedHeaders = implode(';', array_keys(array_change_key_case($headers, CASE_LOWER)));

        $canonicalRequest =
            self::ENDPOINT_METHOD . "\n" .
            "/\n" .
            "\n" .
            $canonicalHeaders . "\n" .
            $signedHeaders . "\n" .
            hash(self::HASH_ALGORITHM, json_encode($params));

        $credentialScope = $date . '/' . self::SERVICE . '/' . self::SIGN_END_DELIMITER;

        $stringToSign =
            self::SIGN_ALGORITHM . "\n" .
            $timestamp . "\n" .
            $credentialScope . "\n" .
            hash(self::HASH_ALGORITHM, $canonicalRequest);

        $secretId = $this->config->get('secret_id');
        $secretKey = $this->config->get('secret_key');
        $secretDate = hash_hmac(self::HASH_ALGORITHM, $date, 'TC3' . $secretKey, true);
        $secretService = hash_hmac(self::HASH_ALGORITHM, self::SERVICE, $secretDate, true);
        $secretSigning = hash_hmac(self::HASH_ALGORITHM, self::SIGN_END_DELIMITER, $secretService, true);

        $signature = hash_hmac(self::HASH_ALGORITHM, $stringToSign, $secretSigning);

        return self::SIGN_ALGORITHM . ' ' .
            'Credential=' . $secretId . '/' . $credentialScope . ', ' .
            'SignedHeaders=' . $signedHeaders . ', ' .
            'Signature=' . $signature;
    }
}
