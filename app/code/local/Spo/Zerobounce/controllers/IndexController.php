<?php

class Spo_Zerobounce_IndexController extends Mage_Core_Controller_Front_Action
{

    private $key = 'e43b95a32d264865b58520f011ccd3f0';
    private $baseURL = "https://api.zerobounce.net/v2/";


    private function api_call($method, array $params)
    {
        $store = Mage::app()->getStore();
        $key = Mage::getStoreConfig('spo_zerobounce/general/zerobounce_api', $store);

        $url = 'https://api.zerobounce.net/v2/validate?api_key='.$key.'&email=' . urlencode($params['email']) . '&ip_address=' . urlencode($params['ip_address']);

        //PHP 5.5.19 and higher has support for TLS 1.2
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 150);
        $response = curl_exec($ch);
        curl_close($ch);

        //decode the json response
        $responseJSON = json_decode($response, true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseJSON));
    }

    public function validate($email, $ip)
    {
        return $this->api_call("validate", ["email" => $email, "ip_address" => $ip]);
    }

    public function indexAction()
    {
        $email = $this->getRequest()->getParam('email');
        $ip = $_SERVER['REMOTE_ADDR'];
        return ($this->validate($email, $ip));
    }


}