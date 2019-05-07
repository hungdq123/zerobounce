<?php

class Spo_Zerobounce_IndexController extends Mage_Core_Controller_Front_Action
{
    
    private $baseURL = "https://api.zerobounce.net/v2/";


    private function api_call($method, array $params)
    {
        $store = Mage::app()->getStore();
        $key = Mage::getStoreConfig('spo_zerobounce/general/zerobounce_api', $store);

        $url = 'https://api.zerobounce.net/v2/validate?api_key='.$key.'&email=' . urlencode($params['email']) . '&ip_address=' . urlencode($params['ip_address']);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 150);
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response, true);


        if($response['status'] == 'invalid') {
            switch ($response['sub_status']) {
                case 'possible_typo':
                    $response['message'] = Mage::helper('zerobounce')->__('Your email address "%s" is invalid, did you mean "%s".', $params['email'], $response['did_you_mean']);
                    break;
                case 'disposable':
                    $response['message'] = Mage::helper('zerobounce')->__('Thanks for your interest in our newsletter, but we don\'t accept temporary email addresses. Please use your real email address.');
                    break;
                case 'role_based':
                    $response['message'] = Mage::helper('zerobounce')->__('Please use your personal email account, we don\'t allow emails that start with "admin, sales, website, etc...');
                    break;
                default:
                    $response['message'] = Mage::helper('zerobounce')->__('Your email is invalid!');
                    break;
            }
        }
        $result = Mage::helper('core')->jsonEncode($response);
        $this->getResponse()->setBody($result);
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
