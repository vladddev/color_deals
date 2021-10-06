<?php

$domain = $_REQUEST['domain'];


define('BASE_DOMAIN', $domain);
define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . BASE_DOMAIN . DIRECTORY_SEPARATOR);
define('TOKEN_FILE', BASE_PATH . 'token_info.json');
define('AMO_FILE', BASE_PATH . 'amo_info.json');
define('SETTINGS_FILE', BASE_PATH . 'settings.json');

include_once __DIR__ . '/bootstrap.php';
include_once __DIR__ . '/color_deals.php';

// writeToLog($_REQUEST);

try {
    $AmoAuth = new AmoAuth();
    $apiClient = $AmoAuth->getApiClient();
    $ColorDeals = new ColorDeals($apiClient);
    $settings = json_decode(file_get_contents(SETTINGS_FILE), true);
    $ColorDeals->color_deals_conditions = $settings['conditions'];

    $ColorDeals->saveColorDealsSettings(true);
} catch (\Throwable $th) {
    writeToLog($th);
}












