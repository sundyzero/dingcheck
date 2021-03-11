<?php

/*
 * This file is part of the mingyoung/dingtalk.
 *
 * (c) 张铭阳 <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk;

use Overtrue\Http\Traits\HasHttpRequests;

class Robot
{
    use HasHttpRequests;

    /**
     * 机器人 AccessToken
     *
     * @var string
     */
    protected $accessToken;

    /**
     * 加签 没有勾选，不用填写
     *
     * @var string
     */
    protected $secret;

    /**
     * @param string      $accessToken
     * @param string|null $secret
     */
    public function __construct($accessToken, $secret = null)
    {
        $this->accessToken = $accessToken;
        $this->secret = $secret;
    }

    /**
     * @param string      $accessToken
     * @param string|null $secret
     *
     * @return self
     */
    public static function create($accessToken, $secret = null)
    {
        return new static($accessToken, $secret);
    }

    /**
     * 发送消息
     *
     * @param array $message
     *
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send($message)
    {
        $url = 'https://oapi.dingtalk.com/robot/send?access_token='.$this->accessToken;

        if ($this->secret) {
            $timestamp = time().'000';
            $url .= sprintf(
                '&sign=%s&timestamp=%s',
                urlencode(base64_encode(hash_hmac('sha256', $timestamp."\n".$this->secret, $this->secret, true))), $timestamp
            );
        }

        $response = $this->getHttpClient()->request(
            'POST', $url, ['json' => $message]
        );

        return $this->castResponseToType($response);
    }
}
