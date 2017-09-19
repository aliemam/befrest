<?php


namespace Aliemam\Befrest;

use Aliemam\Befrest\Exceptions\ApiException;
use Aliemam\Befrest\Traits\Befrest;

class Api
{
    use Befrest;

    public $response;

    /**
     * Api constructor.
     *
     * @param null $config
     * @throws ApiException
     */
    public function __construct($config = null) {
        if(!isset($config))
            throw new ApiException('Cant generate Befrest Api Object: config not set yet!!!');
        $this->config = $config;
    }

    /**
     * this function executes api call and retrieves result
     *
     * @param $api_call
     * @param $message
     * @return mixed
     * @throws ApiException
     */
    public function run($api_call, $message) {

        $curl = curl_init($api_call['addr']);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $api_call['method']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $api_call['headers']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $message);
        curl_exec($curl);

        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($curl);
        $curl_error = curl_error($curl);
        if( $curl_errno ) {
            throw new ApiException($curl_error, $curl_errno);
        }
        else if( $code != 200 ) {
            throw new ApiException("Request has errors", $code);
        }

        $this->response = $response;
        $res = json_decode($response, TRUE);
        if( isset($res['type']) && $res['type'] == 'error' ) {
            throw new ApiException($res['message'], '500');
        }

        return $res;

    }

    public function getRawRes(){
        return $this->response;
    }

}
?>
