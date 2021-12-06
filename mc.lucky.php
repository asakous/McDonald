<?php
$config['useragent'] = 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_2 like Mac OS X; nl-nl) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8H7 Safari/6533.18.5';

$password = "1s2unxaounk8zusv";
$cipher ="AES-128-ECB";
$accessToken=''; /*請找出你自已的token*/
$data = array(
"type"=>"ENVELOPE",
"pageSize"=>50,
"startIndex"=>0
);

$game_list_enc=json_encode($data);
$chiperRaw = openssl_encrypt($game_list_enc, $cipher, $password, OPENSSL_RAW_DATA);
$ciphertext = trim(base64_encode($chiperRaw));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://mcdapi.mcddailyapp.com.tw/McDonaldAPI/game/list');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
curl_setopt($ch, CURLOPT_POSTFIELDS,$ciphertext);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
   'Content-Type: application/json',
    'accessToken: '.$accessToken
]);
$result = json_decode(curl_exec($ch));
$chiperRaw = base64_decode($result->data);
$result_game = json_decode(openssl_decrypt($chiperRaw, $cipher, $password, OPENSSL_RAW_DATA));/*解出任務資料*/
$game_id=array();

foreach($result_game->list as $game) {
	
	if ($game->type=='ROULETTE' || $game->type=='SCRAPINGCARD' || $game->type=='ENVELOPE' || $game->type=='QUESTION' ) { /*目前已知可吃轉盤，刮刮樂，抽卡跟問卷 */
		$game_id[] = $game->id;
	}
		
	
}
foreach ($game_id as $id ) {
	$game_data = array(
	"gameId"=>$id
	);
	$game_id_enc=json_encode($game_data);
	$chiperRaw = openssl_encrypt($game_id_enc, $cipher, $password, OPENSSL_RAW_DATA);
	$ciphertext = trim(base64_encode($chiperRaw));
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://mcdapi.mcddailyapp.com.tw/McDonaldAPI/h5/game/joinGame');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$game_id_enc);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
	   'Content-Type: application/json',
	    'accessToken: '.$accessToken
	]);
	$result = json_decode(curl_exec($ch));
	print_r($result);
}
