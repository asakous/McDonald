<?php
$config['useragent'] = 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_2 like Mac OS X; nl-nl) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8H7 Safari/6533.18.5';

$password = "1s2unxaounk8zusv";
$cipher ="AES-128-ECB";
$accessToken=''; /*請找出你自已的token*/
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://mcdapi.mcddailyapp.com.tw/McDonaldAPI/game/checkIn/detail');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
   'Content-Type: application/json',
   'accessToken: '.$accessToken
]);
$result_login = json_decode(curl_exec($ch));
curl_close($ch);

$chiperRaw = base64_decode($result_login->data);
$result_game = json_decode(openssl_decrypt($chiperRaw, $cipher, $password, OPENSSL_RAW_DATA));

$enc=json_encode(array('gameId'=>$result_game->gameCheckInVo->gameId));
$chiperRaw = openssl_encrypt($enc, $cipher, $password, OPENSSL_RAW_DATA); /*解出資料*/
$ciphertext = trim(base64_encode($chiperRaw));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://mcdapi.mcddailyapp.com.tw/McDonaldAPI/game/joinGame');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $ciphertext);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
   'Content-Type: application/json',
   'accessToken: '.$accessToken
]);
$result_checkin = json_decode(curl_exec($ch));

print_r($result_checkin);
$chiperRaw = base64_decode($result_checkin->data);
$result_game = json_decode(openssl_decrypt($chiperRaw, $cipher, $password, OPENSSL_RAW_DATA)); /*解出資料*/

print_r($result_game) ;