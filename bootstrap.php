<?php

include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/src/AmoCRM.php';
use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\OAuth2\Client\Provider\AmoCRM;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\WebhookModel;
use League\OAuth2\Client\Token\AccessTokenInterface;


class AmoAuth {
    public function __construct($domain = BASE_DOMAIN) {
        $this->domain = $domain;
        $this->base_path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $domain . DIRECTORY_SEPARATOR;
        $this->amo_path = $this->base_path . 'amo_info.json';
        $this->token_path = $this->base_path . 'token_info.json';
        $this->amo_settings = $this->getAmo();
        $this->apiClient = new AmoCRMApiClient($this->amo_settings['clientId'], $this->amo_settings['clientSecret'], 'https://core.market.ru/color_deals/index.php');
        $this->accessToken = $this->getToken();

        
        // echo '<pre>';
        // print_r($this->accessToken);
        // echo '</pre><br>';

        if (time() > $this->accessToken->getExpires()) {
            
            $data = [
                'client_id' => $this->amo_settings['clientId'],
                'client_secret' => $this->amo_settings['clientSecret'],
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->accessToken->getRefreshToken(),
                'redirect_uri' => 'https://core.market.ru/color_deals/index.php',
            ];

            // echo '<pre>';
            // print_r($data);
            // echo '</pre><br>';


            $link = 'https://' . $domain . '/oauth2/access_token'; 
            $curl = curl_init(); 
            
            curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
            curl_setopt($curl,CURLOPT_URL, $link);
            curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
            curl_setopt($curl,CURLOPT_HEADER, false);
            curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);

            $out = curl_exec($curl);
            
            curl_close($curl);

            $response = json_decode($out, true);
            // echo '<pre>';
            // print_r($response);
            // echo '</pre><br>';
            $this->saveToken([
                'accessToken' => $response['access_token'],
                'refreshToken' => $response['refresh_token'],
                'expires' => time() + (int)$response['expires_in'],
                'baseDomain' => $domain,
            ]);

            $this->accessToken = $this->getToken();
        }

        $this->apiClient->setAccessToken($this->accessToken)
        ->setAccountBaseDomain($domain)
        ->onAccessTokenRefresh(
            function (AccessTokenInterface $accessToken, string $baseDomain) {
                $this->saveToken([
                    'accessToken' => $accessToken->getToken(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'baseDomain' => $domain,
                ]);
            }
        );
    }

    public function getApiClient() {
        return $this->apiClient;
    }


    private function saveToken($accessToken, $file = null) {
        $file_name = $file ? : TOKEN_FILE;
        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            $data = [
                'accessToken' => $accessToken['accessToken'],
                'expires' => $accessToken['expires'],
                'refreshToken' => $accessToken['refreshToken'],
                'baseDomain' => $accessToken['baseDomain'],
            ];

            file_put_contents($file_name, json_encode($data));
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }
    private function getToken($file = null) {
        $file_name = $file ? : TOKEN_FILE;
        $accessToken = json_decode(file_get_contents($file_name), true);

        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            return new \League\OAuth2\Client\Token\AccessToken([
                'access_token' => $accessToken['accessToken'],
                'refresh_token' => $accessToken['refreshToken'],
                'expires' => $accessToken['expires'],
                'baseDomain' => $accessToken['baseDomain'],
            ]);
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }
    private function getAmo($file = null) {
        $file_name = $file ? : AMO_FILE;
        $handle = fopen($file_name, 'r+');
        $settings_file_content = json_decode(fread($handle, filesize($file_name)), true);
        fclose($handle);

        return $settings_file_content;    
    }
    private function saveAmo($data, $file = null) {
        $file_name = $file ? : AMO_FILE;
        $handle = fopen($file_name, 'w+');
        fwrite($handle, json_encode($data));
        fclose($handle);
    }
}







