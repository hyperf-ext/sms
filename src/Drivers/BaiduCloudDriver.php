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

/**
 * @see https://cloud.baidu.com/doc/SMS/API.html
 */
class BaiduCloudDriver extends AbstractDriver
{
    protected const ENDPOINT_HOST = 'sms.bj.baidubce.com';

    protected const ENDPOINT_URI = '/bce/v2/message';

    protected const BCE_AUTH_VERSION = 'bce-auth-v1';

    protected const DEFAULT_EXPIRATION_IN_SECONDS = 1800; //签名有效期默认1800秒

    protected const SUCCESS_CODE = 1000;

    public function send(SmsMessageInterface $message): array
    {
        $params = [
            'invokeId' => $this->config->get('invoke_id'),
            'phoneNumber' => $message->to->getNationalNumber(),
            'templateCode' => $message->template,
            'contentVar' => $message->data,
        ];

        $datetime = gmdate('Y-m-d\TH:i:s\Z');

        $headers = [
            'host' => self::ENDPOINT_HOST,
            'content-type' => 'application/json',
            'x-bce-date' => $datetime,
            'x-bce-content-sha256' => hash('sha256', json_encode($params)),
        ];
        //获得需要签名的数据
        $signHeaders = $this->getHeadersToSign($headers, ['host', 'x-bce-content-sha256']);

        $headers['Authorization'] = $this->generateSign($signHeaders, $datetime);

        $response = $this->client->postJson($this->buildEndpoint(), $params, $headers);

        $result = $response->toArray();

        if ($result['code'] != self::SUCCESS_CODE) {
            throw new DriverErrorException($result['message'], $result['code'], $response);
        }

        return $result;
    }

    protected function buildEndpoint(): string
    {
        return 'http://' . $this->config->get('domain', self::ENDPOINT_HOST) . self::ENDPOINT_URI;
    }

    protected function generateSign(array $signHeaders, string $datetime): string
    {
        // 生成 authString
        $authString = self::BCE_AUTH_VERSION . '/' . $this->config->get('ak') . '/'
            . $datetime . '/' . self::DEFAULT_EXPIRATION_IN_SECONDS;

        // 使用 sk 和 authString 生成 signKey
        $signingKey = hash_hmac('sha256', $authString, $this->config->get('sk'));
        // 生成标准化 URI
        // 根据 RFC 3986，除了：1.大小写英文字符 2.阿拉伯数字 3.点'.'、波浪线'~'、减号'-'以及下划线'_' 以外都要编码
        $canonicalURI = str_replace('%2F', '/', rawurlencode(self::ENDPOINT_URI));

        // 生成标准化 QueryString
        $canonicalQueryString = ''; // 此 api 不需要此项。返回空字符串

        // 整理 headersToSign，以 ';' 号连接
        $signedHeaders = empty($signHeaders) ? '' : strtolower(trim(implode(';', array_keys($signHeaders))));

        // 生成标准化 header
        $canonicalHeader = $this->getCanonicalHeaders($signHeaders);

        // 组成标准请求串
        $canonicalRequest = "POST\n{$canonicalURI}\n{$canonicalQueryString}\n{$canonicalHeader}";

        // 使用 signKey 和标准请求串完成签名
        $signature = hash_hmac('sha256', $canonicalRequest, $signingKey);

        // 组成最终签名串
        return "{$authString}/{$signedHeaders}/{$signature}";
    }

    protected function getCanonicalHeaders(array $headers): string
    {
        $headerStrings = [];
        foreach ($headers as $name => $value) {
            //trim后再encode，之后使用':'号连接起来
            $headerStrings[] = rawurlencode(strtolower(trim($name))) . ':' . rawurlencode(trim($value));
        }

        sort($headerStrings);

        return implode("\n", $headerStrings);
    }

    protected function getHeadersToSign(array $headers, array $keys): array
    {
        return array_intersect_key($headers, array_flip($keys));
    }
}
