<?php

function writeToLog($data, $title = '') { 
    $log = "\n------------------------\n"; 
    $log .= date("Y.m.d G:i:s") . "\n"; 
    $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n"; 
    $log .= print_r($data, 1); 
    $log .= "\n------------------------\n"; 
    file_put_contents(getcwd() . '/hook.log', $log, FILE_APPEND); 
    return true; 
} 

$domain = $_REQUEST['account']['subdomain'] . '.amocrm.ru';

// writeToLog($_REQUEST);

$url = 'http://core.market.ru/color_deals/hook_worker.php';
$ch = curl_init();  

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'domain' => $domain,
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);

$content = curl_exec($ch); 

curl_close($ch);







