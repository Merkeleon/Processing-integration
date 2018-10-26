<?php

namespace Merkeleon\Processing;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class API
{
    protected $uri;
    protected $apiKey;
    protected $apiSecret;

    protected function __construct($uri, $apiKey, $apiSecret)
    {
        $this->uri       = $uri;
        $this->apiKey    = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public static function make($uri, $apiKey, $apiSecret)
    {
        return new static($uri, $apiKey, $apiSecret);
    }

    private function request()
    {
        $client = new Client([
            'base_uri' => $this->uri,
        ]);

        return $client;
    }

    private function createRequestHeaders($params = [])
    {
        $signature = hash_hmac("sha512", \GuzzleHttp\json_encode($params), $this->apiSecret);

        return [
            'X-Processing-Key'       => $this->apiKey,
            'X-Processing-Signature' => $signature
        ];
    }

    private function post($uri, $params = [])
    {
        try
        {
            $response = $this->request()
                             ->post($uri, [
                                 'json'    => $params,
                                 'headers' => $this->createRequestHeaders($params)
                             ]);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)
            {
                return \GuzzleHttp\json_decode($response->getBody()
                                                        ->getContents(), true);
            }
        }
        catch (ClientException $e)
        {
            try
            {
                $response = \GuzzleHttp\json_decode($e->getResponse()
                                                      ->getBody(), true);

                return $response;
            }
            catch (\InvalidArgumentException $e)
            {
                return false;
            }
        }

        return false;
    }

    public function withdraw($address, $amount, $currency, $foreignId, $tag)
    {
        return $this->post('withdrawal/make', [
            'currency'   => $currency,
            'address'    => $address,
            'amount'     => $amount,
            'foreign_id' => $foreignId,
            'tag'        => $tag
        ]);
    }

    /**
     * @return Address|bool
     */
    public function addressTake($iso, $foreignId)
    {
        $return = false;

        $response = $this->post('addresses/take', [
            'currency'   => $iso,
            'foreign_id' => $foreignId
        ]);

        if ($response)
        {
            if (array_has($response, 'data.address'))
            {
                $return          = new Address();
                $return->address = array_get($response, 'data.address');
                $return->tag     = array_get($response, 'data.tag');
                $return->origin  = array_get($response, 'data.origin');
            }
        }

        return $return;
    }
}