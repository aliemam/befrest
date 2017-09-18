<?php
/**
 * Created by PhpStorm.
 * User: mirage
 * Date: 4/4/17
 * Time: 2:51 PM
 */

namespace Befrest\Traits;

use Befrest\Exceptions\ApiException;


trait Befrest {

    public $config = null;
    
    /**
     * @param       $chid
     * @param array $topics
     * @return mixed اتصال کانال به بفرست
     * اتصال کانال به بفرست
     * this function is not for api providers just for talking 2 or more servers with each other you have to subscribe
     * your servers to befrest to let it downstream messages to your servers
     */
    public function generateSubscribeApi($chid, $topics=[]){
        $addr = '/xapi/'.$this->config['api_version'].'/subscribe/'.$this->config['uid'].'/'.$chid.'/'.$this->config['sdk_version'];
        return [
            'addr' => $this->config['api_address'].$addr,
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
    public function generatePublishToChannelApi($chid, $hours=240){
        if($hours > 336)
            $hours = 336;
        $addr = '/xapi/'.$this->config['api_version'].'/publish/'.$this->config['uid'].'/'.$chid;
        return [
            'addr' => $this->config['api_address'].$addr,
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
    public function generatePublishToChannelStatusApi($mid){
        $addr = '/xapi/'.$this->config['api_version'].'/message-status/'.$this->config['uid'].'/'.$mid;
        return [
            'addr' => $this->config['api_address'].$addr,
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
    public function generateMultiPublishApi($chids=[], $hours=240){
        if($hours > 336)
            $hours = 336;

        $addr = '/xapi/'.$this->config['api_version'].'/multi-publish/'.$this->config['uid'];
        return [
            'addr' => $this->config['api_address'].$addr,
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
    public function generatePublishToTopicApi($topic, $hours=240){
        if($hours > 336)
            $hours = 336;

        $addr = '/xapi/'.$this->config['api_version'].'/t-publish/'.$this->config['uid'].'/'.$topic;
        return [
            'addr' => $this->config['api_address'].$addr,
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
    public function generatePublishToTopicStatusApi($topic, $mid){
        $addr = '/xapi/'.$this->config['api_version'].'/topic-msg-status/'.$this->config['uid'].'/'.$topic.'/'.$mid;
        return [
            'addr' => $this->config['api_address'].$addr,
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
    public function generateTopicStatusApi($topic){
        $addr = '/xapi/'.$this->config['api_version'].'/topic-status/'.$this->config['uid'].'/'.$topic;
        return [
            'addr' => $this->config['api_address'].$addr,
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
    public function generateChannelStatusApi($chid){
        $addr = '/xapi/'.$this->config['api_version'].'/channel-status/'.$this->config['uid'].'/'.$chid;
        return [
            'addr' => $this->config['api_address'].$addr,
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
    public function generateAuth($addr = null) {
        if(!isset($addr))
            throw new ApiException('Cant generate valid auth key: api address not defined yet!!!');
        $payload = self::base64Encode(hex2bin(md5(sprintf('%s,%s', $this->config['api_key'], $addr))));
        return self::base64Encode(hex2bin(md5(sprintf('%s,%s', $this->config['shared_key'], $payload))));
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
