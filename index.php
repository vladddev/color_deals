<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");

$dir = $_GET['domain'];
define('BASE_DOMAIN', $dir);
define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR);
define('TOKEN_FILE', BASE_PATH . 'token_info.json');
define('AMO_FILE', BASE_PATH . 'amo_info.json');
define('SETTINGS_FILE', BASE_PATH . 'settings.json');

if (!isset($_GET['clientId']) && !isset($_GET['clientSecret']) && (!isset($_GET['request']) && !isset($_GET['referer']))) { ?>

<div class="wrap">
    <div class="container">
        <form method="GET">
            <label>Домен(полностью): <input type="text" name="domain"></label> <br>
            <label>ID интеграции: <input type="text" name="clientId"></label> <br>
            <label>Секретный ключ: <input type="text" name="clientSecret"></label> <br>
            <button type="submit">Отправить</button>
        </form>
    </div>
</div>

<style>
.wrap {
    padding: 100px 30px;
}
.container {
    width: 400px;
    margin: auto
}

</style>
    
<?php 

return; 

}


// try {
// include_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/color_deals.php';
// include_once __DIR__ . '/src/AmoCRM.php';
use AmoCRM\Client\AmoCRMApiClient;
// use AmoCRM\OAuth2\Client\Provider\AmoCRM;
// use AmoCRM\Exceptions\AmoCRMApiException;
// use AmoCRM\Models\WebhookModel;
// use League\OAuth2\Client\Token\AccessTokenInterface;


session_start();

/**
 * Создаем провайдера
 */
if (isset($_GET['clientId'])) {
    $_SESSION['widget_clientId'] = $_GET['clientId'];
}
if (isset($_GET['clientSecret'])) {
    $_SESSION['widget_clientSecret'] = $_GET['clientSecret'];
}




if (!isset($_GET['request'])) {
    // try {
        
    
    mkdir('data/' . $dir);
    $amo_data = getAmo();
    $clientId = $_SESSION['widget_clientId'] ? : $amo_data['clientId'];
    $clientSecret = $_SESSION['widget_clientSecret'] ? : $amo_data['clientSecret'];
    $apiClient = new AmoCRMApiClient($_SESSION['widget_clientId'], $_SESSION['widget_clientSecret'], 'https://core.market.ru/color_deals/index.php');

    if (isset($_GET['referer'])) {
        $apiClient->setAccountBaseDomain($_GET['referer']);
    }

    // } catch (\Throwable $th) {
    //     print_r($th);
    // }

    if (!isset($_GET['code'])) {
        /**
         * Просто отображаем кнопку авторизации или получаем ссылку для авторизации
         * По-умолчанию - отображаем кнопку
         */
        $_SESSION['oauth2state'] = bin2hex(random_bytes(16));
        if (true) {
            echo $apiClient->getOAuthClient()->getOAuthButton(
                [
                    'title' => 'Установить Цветные сделки',
                    'compact' => false,
                    'class_name' => 'className',
                    'color' => 'default',
                    'error_callback' => 'handleOauthError',
                    'state' => $_SESSION['oauth2state'],
                ]
            );
            
            die;
        } else {
            $authorizationUrl = $apiClient->getOAuthClient()->getAuthorizeUrl([
                'state' => $_SESSION['oauth2state'],
                'mode' => 'post_message',
            ]);
            header('Location: ' . $authorizationUrl);
            die;
        }
    } elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
        exit('Invalid state');
    }

    /**
     * Ловим обратный код
     */
    try {
        /** @var \League\OAuth2\Client\Token\AccessToken $access_token */
        $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);

        echo '! ', $apiClient->getAccountBaseDomain(), ' !<br>';

        if (!$accessToken->hasExpired()) {
            $file1 = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $apiClient->getAccountBaseDomain() . DIRECTORY_SEPARATOR . 'token_info.json';
            $file2 = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $apiClient->getAccountBaseDomain() . DIRECTORY_SEPARATOR . 'settings.json';
            $file3 = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $apiClient->getAccountBaseDomain() . DIRECTORY_SEPARATOR . 'amo_info.json';
            
            
            saveToken([
                'accessToken' => $accessToken->getToken(),
                'refreshToken' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
                'baseDomain' => $apiClient->getAccountBaseDomain(),
            ], $file1);    
            saveSettings([], [], $file2);
            saveAmo([
                'clientId' => $_SESSION['widget_clientId'],
                'clientSecret' => $_SESSION['widget_clientSecret']
            ], $file3);    
            
        } else {
            print_r($accessToken);
            echo '!!!<br>';
            echo '!!!<br>';
        }

        unset($_SESSION['widget_clientId']);
        unset($_SESSION['widget_clientSecret']);
    } catch (Exception $e) {
        die((string)$e);
    }

    /** @var \AmoCRM\OAuth2\Client\Provider\AmoCRMResourceOwner $ownerDetails */
    $ownerDetails = $apiClient->getOAuthClient()->getResourceOwner($accessToken);

    printf('Hello, %s!', $ownerDetails->getName());


    $webhook = new WebhookModel();
    $webhook->setDestination('https://core.market.ru/color_deals/worker.php')
        ->setSettings([
            'add_lead',
            'update_lead'
        ]);

    $apiClient->webhooks()->subscribe($webhook);
  

} else {

    header('Content-Type: application/json');
    if ($_GET['request'] == 'get') {
        echo getSettings();
    } else if ($_GET['request'] == 'save') {
        try {
            $AmoAuth = new AmoAuth();
            $apiClient = $AmoAuth->getApiClient();
            $ColorDeals = new ColorDeals($apiClient);

            $ColorDeals->saveColorDealsSettings();
        } catch (\Throwable $th) {
            print_r($th);
        }
        
        // saveSettings($_POST, [], );
        echo getSettings();
    } else {
        echo 'unknown request';
    }
        

        // $data = $provider->getHttpClient()
        //     ->request('GET', $provider->urlAccount() . 'api/v2/account', [
        //         'headers' => $provider->getHeaders($accessToken)
        //     ]);

        // $parsedBody = json_decode($data->getBody()->getContents(), true);
        // printf('ID аккаунта - %s, название - %s', $parsedBody['id'], $parsedBody['name']);
   
}



function saveToken($accessToken, $file = null)
{
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

        $handle = fopen($file_name, 'w+');
        fwrite($handle, json_encode($data));
        fclose($handle);

        // file_put_contents($file_name, json_encode($data));
    } else {
        exit('Invalid access token ' . var_export($accessToken, true));
    }
}

function getToken($file = null)
{
    $file_name = $file ? : TOKEN_FILE;
    // $accessToken = json_decode(file_get_contents($file_name), true);

    $handle = fopen($file_name, 'r+');
    $accessToken = json_decode(fread($handle, filesize($file_name)), true);
    fclose($handle);

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

function getSettings($file = null)
{
    $file_name = $file ? : SETTINGS_FILE;
    $handle = fopen($file_name, 'r+');
    $file_content = fread($handle, filesize($file_name));
    fclose($handle);

    return $file_content;
}

function saveSettings($data, $deals = [], $file = null)
{
    $file_name = $file ? : SETTINGS_FILE;
    $handle = fopen($file_name, 'w+');
    $new_settings = [
        'conditions' => $data['data'],
        'deals' => $deals
    ];
    
    fwrite($handle, json_encode($new_settings));
    fclose($handle);
}

function getAmo($file = null)
{
    $file_name = $file ? : AMO_FILE;
    $handle = fopen($file_name, 'r+');
    $settings_file_content = json_decode(fread($handle, filesize($file_name)), true);
    fclose($handle);

    return $settings_file_content;    
}

function saveAmo($data, $file = null)
{
    $file_name = $file ? : AMO_FILE;
    $handle = fopen($file_name, 'w+');
    fwrite($handle, json_encode($data));
    fclose($handle);
}
