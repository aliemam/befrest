<?php
/**
 * Created by PhpStorm.
 * User: mirage
 * Date: 4/4/17
 * Time: 2:51 PM
 */

namespace Befrest\Traits;

use Befrest\Exceptions\ApiException;
use function Sodium\add;


trait Befrest {

    /**
     * @param       $chid
     * @param array $topics
     * @return mixed اتصال کانال به بفرست
     * اتصال کانال به بفرست
     * this function is not for api providers just for talking 2 or more servers with each other you have to subscribe
     * your servers to befrest to let it downstream messages to your servers
     */
    static function generateSubscribeApi($chid, $topics=[]){
        $addr = '/xapi/'.Constants::API_VERSION.'/subscribe/'.Constants::UID.'/'.$chid.'/'.Constants::SDK_VERSION;
        return [
            'addr' => Constants::API_ADDRESS.$addr,
            'headers' => [
                'X-BF-AUTH: '.self::generateAuth($addr),
                'X-BF-TOPICS: all-'.join('-', $topics)
            ],
            'method' => 'GET'
        ];
    }

    /**
     * @param     $chid
     * @param int $hours
     * @return mixed ارسال پیام به کانال
     * ارسال پیام به کانال
     */
    static function generatePublishToChannelApi($chid, $hours=240){
        if($hours > 336)
            $hours = 336;
        $addr = '/xapi/'.Constants::API_VERSION.'/publish/'.Constants::UID.'/'.$chid;
        return [
            'addr' => Constants::API_ADDRESS.$addr,
            'headers' => [
                'X-BF-AUTH: '.self::generateAuth($addr),
                'X-BF-TTL: '.($hours*60*60)
            ],
            'method' => 'POST'
        ];
    }

    /**
     * @param $mid
     * @return mixed
     * بررسی وضعیت پیام
     */
    static function generatePublishToChannelStatusApi($mid){
        $addr = '/xapi/'.Constants::API_VERSION.'/message-status/'.Constants::UID.'/'.$mid;
        return [
            'addr' => Constants::API_ADDRESS.$addr,
            'headers' => [
                'X-BF-AUTH: '.self::generateAuth($addr),
            ],
            'method' => 'GET'
        ];
    }

    /**
     * @param array $chids
     * @param int   $hours
     * @return mixed ارسال پیام گروهی
     * ارسال پیام گروهی
     */
    static function generateMultiPublishApi($chids=[], $hours=240){
        if($hours > 336)
            $hours = 336;

        $addr = '/xapi/'.Constants::API_VERSION.'/multi-publish/'.Constants::UID;
        return [
            'addr' => Constants::API_ADDRESS.$addr,
            'headers' => [
                'X-BF-AUTH: '.self::generateAuth($addr),
                'X-BF-TTL: '.($hours*60*60),
                'X-BF-CH: '.join('-', $chids)
            ],
            'method' => 'POST'
        ];
    }

    /**
     * @param $topic
     * @return mixed
     * ارسال پیام تاپیک
     */
    static function generatePublishToTopicApi($topic, $hours=240){
        if($hours > 336)
            $hours = 336;

        $addr = '/xapi/'.Constants::API_VERSION.'/t-publish/'.Constants::UID.'/'.$topic;
        return [
            'addr' => Constants::API_ADDRESS.$addr,
            'headers' => [
                'X-BF-AUTH: '.self::generateAuth($addr),
                'X-BF-TEXP: '.($hours*60*60)
            ],
            'method' => 'POST'
        ];
    }

    /**
     * @param $topic
     * @param $mid
     * @return mixed
     * بررسی وضعیت پیام تاپیک
     */
    static function generatePublishToTopicStatusApi($topic, $mid){
        $addr = '/xapi/'.Constants::API_VERSION.'/topic-msg-status/'.Constants::UID.'/'.$topic.'/'.$mid;
        return [
            'addr' => Constants::API_ADDRESS.$addr,
            'headers' => [
                'X-BF-AUTH: '.self::generateAuth($addr),
            ],
            'method' => 'GET'
        ];
    }

    /**
     * @param $topic
     * @return mixed
     * بررسی وضعیت تاپیک
     */
    static function generateTopicStatusApi($topic){
        $addr = '/xapi/'.Constants::API_VERSION.'/topic-status/'.Constants::UID.'/'.$topic;
        return [
            'addr' => Constants::API_ADDRESS.$addr,
            'headers' => [
                'X-BF-AUTH: '.self::generateAuth($addr),
            ],
            'method' => 'GET'
        ];
    }

    /**
     * @param $chid
     * @return mixed
     * بررسی وضعیت کانال
     */
    static function generateChannelStatusApi($chid){
        $addr = '/xapi/'.Constants::API_VERSION.'/channel-status/'.Constants::UID.'/'.$chid;
        return [
            'addr' => Constants::API_ADDRESS.$addr,
            'headers' => [
                'X-BF-AUTH: '.self::generateAuth($addr),
            ],
            'method' => 'GET'
        ];
    }

    /**
     * @param string $addr
     * @return mixed
     * generate auth for given path
     * @throws ApiException
     */
    static function generateAuth($addr = null) {
        if(!isset($addr))
            throw new ApiException('Cant generate valid auth key: api address not defined yet!!!');
        $payload = self::base64Encode(hex2bin(md5(sprintf('%s,%s', Constants::API_KEY, $addr))));
        return self::base64Encode(hex2bin(md5(sprintf('%s,%s', Constants::SDK_VERSION, $payload))));
    }

    /**
     * @param $input
     * @return mixed
     * base64 encoder for befrest api
     */
    static function base64Encode($input) {
        $payload = str_replace('+', '-', base64_encode($input));
        $payload = str_replace('=', '', $payload);
        return str_replace('/', '_', $payload);
    }
}
