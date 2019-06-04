<?php

namespace Khatfield\LaravelYtel;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Khatfield\LaravelYtel\Exceptions\YtelException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Ytel
{
    private $sid;
    private $token;
    private $base_url = 'https://api.ytel.com/api/v3/';

    protected $connected = false;
    protected $client;

    /**
     * Ytel constructor.
     *
     * @param Collection $config
     */
    public function __construct($config)
    {
        $this->sid = $config->get('ytel.sid');
        $this->token = $config->get('ytel.token');

        if(!empty($this->sid) && !empty($this->token)){
            $this->client = new Client(
                [
                    'base_uri' => $this->base_url
                ]
            );
            $this->connected = true;
        }
    }

    /**
     * @param $sms_id
     *
     * @throws YtelException
     *
     * @return mixed
     */
    public function getSmsDetails($sms_id)
    {
        $data = [
            'MesssageSid' => $sms_id,
        ];

        return $this->_doRequest('sms/viewsms', $data);
    }

    /**
     * @param Carbon|\DateTime|null $date
     *
     * @return Collection
     * @throws YtelException
     */
    public function getInboundSms($date = null)
    {
        $endpoint = 'sms/getinboundsms';
        $return = [];
        $data = [
            'PageSize' => 100,
            'Page' => 1
        ];

        if(!is_null($date)){
            $data['DateSent'] = $date->format('Y-m-d');
        }

        $done = false;
        while(!$done){
            $result = $this->_doRequest($endpoint, $data);
            if($result->Message360->ResponseStatus == 1) {
                if($result->Message360->MessageCount < $result->Message360->PageSize){
                    $done = true;
                }
                $return = array_merge($return, $result->Message360->Messages->Message);
            } else {
                throw new YtelException($result->Message360->Errors->Error);
            }
            $data['Page']++;
        }

        return collect($return);
    }

    /**
     * @param string $endpoint
     * @param array  $data
     * @param string $method
     *
     * @throws YtelException
     *
     * @return mixed
     */
    protected function _doRequest($endpoint, $data, $method = 'get')
    {
        if(!$this->connected){
            throw new YtelException('YTEL is Not Connected');
        }

        $headers = [
            'Accept' => 'application/json'
        ];

        $auth = [
            $this->sid,
            $this->token
        ];

        if(stripos($endpoint, '.json') === false){
            $endpoint = $endpoint . '.json';
        }

        $method = strtoupper($method);

        $options = compact('headers', 'auth');
        if($method === 'POST'){
            $options['form_params'] = $data;
        } elseif($method === 'GET') {
            $options['query'] = $data;
        }

        try{
            $response = $this->client->request($method, $endpoint, $options);

            return json_decode($response->getBody()->getContents());
        } catch(GuzzleException $e){
            throw new YtelException('Error Processing YTEL Request: ' . $e->getMessage(), 0, $e);
        }
    }

}