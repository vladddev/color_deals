<?php


$dir = $_REQUEST['amodomain'] . '.amocrm.ru';

define('BASE_DOMAIN', $dir);
define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . BASE_DOMAIN . DIRECTORY_SEPARATOR);
define('TOKEN_FILE', BASE_PATH . 'token_info.json');
define('AMO_FILE', BASE_PATH . 'amo_info.json');
define('SETTINGS_FILE', BASE_PATH . 'settings.json');
define('URL_CORE', 'https://core.market.ru/color_deals/');

$js_file = file_get_contents(__DIR__ . '/scripts/main.js');

$settings = file_get_contents(SETTINGS_FILE) ? : 'false';

$incScripts = '';
$incScripts .= '<script id="fancybox-script" type="text/javascript" src="'.URL_CORE.'js/fancybox.js"></script>';
$incScripts .= '<link id="fancybox-styles" rel="stylesheet" href="'.URL_CORE.'css/fancybox.css">';
$incScripts .= '<link id="core-styles" rel="stylesheet" href="'.URL_CORE.'css/core.css?v1.0.3">';


$js_file = str_replace('[$settings]', 'window.market.ColorDealsSettings = ' . $settings . ';', $js_file);
$js_file = str_replace('[incScripts]', $incScripts, $js_file);


echo $js_file;



