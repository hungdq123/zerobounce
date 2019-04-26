<?php

class Spo_Zerobounce_IndexController extends Mage_Core_Controller_Front_Action
{

    private $key = 'e43b95a32d264865b58520f011ccd3f0';
    private $baseURL = "https://api.zerobounce.net/v2/";


    private function api_call($method, array $params)
    {
        $params['api_key'] = $this->key;
        $paramsURI = http_build_query($params);
        $url = "{$this->baseURL}{$method}?{$paramsURI}";
        if (!isset($this->ch)) {
            $ch = $this->ch = curl_init();
            curl_setopt($ch, CURLOPT_SSLVERSION, 6);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($this->ch, CURLOPT_TIMEOUT, 150);
        }
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $response = curl_exec($this->ch);
        $responseJSON = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid Response", 1);
        }
        if (isset($response['error'])) {
            throw new \Exception($response['error'], 2);
        }
        return $responseJSON;
    }

    public function validate($email, $ip)
    {
        return $this->api_call("validate", ["email" => $email, "ip_address" => $ip]);
    }

    public function indexAction()
    {
        $email = $_POST["value"];
        $ip = $_SERVER['REMOTE_ADDR'];
        return ($this->validate($email, $ip));
    }


}