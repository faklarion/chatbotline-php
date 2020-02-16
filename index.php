<?php

require __DIR__ . '/vendor/autoload.php';


use \LINE\LINEBot\SignatureValidator as SignatureValidator;

// load config
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// initiate app
$configs =  [
	'settings' => ['displayErrorDetails' => true],
];
$app = new Slim\App($configs);

/* ROUTES */
$app->get('/', function ($request, $response) {
	return "Lanjutkan!";
});

$app->post('/', function ($request, $response)
{
	// get request body and line signature header
	$body 	   = file_get_contents('php://input');
	$signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

	// log body and signature
	file_put_contents('php://stderr', 'Body: '.$body);

	// is LINE_SIGNATURE exists in request header?
	if (empty($signature)){
		return $response->withStatus(400, 'Signature not set');
	}

	// is this request comes from LINE?
	if($_ENV['PASS_SIGNATURE'] == false && ! SignatureValidator::validateSignature($body, $_ENV['CHANNEL_SECRET'], $signature)){
		return $response->withStatus(400, 'Invalid signature');
	}

	// init bot
	$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($_ENV['CHANNEL_ACCESS_TOKEN']);
	$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $_ENV['CHANNEL_SECRET']]);
	$data = json_decode($body, true);
	foreach ($data['events'] as $event)
	{
		$userMessage = $event['message']['text'];
		if(strtolower($userMessage) == '/menu')
		{
			$message = "List menu : 
									/subuh
									/zuhur
									/ashar
									/magrib
									/isya";
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
			$result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
			return $result->getHTTPStatus() . ' ' . $result->getRawBody();
		
		} elseif(strtolower($userMessage) == '/subuh')
		{
			$message = "Jadwal sholat subuh di Banjarmasin yaitu 05.10 WITA";
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
			$result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
			return $result->getHTTPStatus() . ' ' . $result->getRawBody();
		
		} elseif(strtolower($userMessage) == '/zuhur')
		{
			$message = "Jadwal sholat zuhur di Banjarmasin yaitu 12.36 WITA";
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
			$result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
			return $result->getHTTPStatus() . ' ' . $result->getRawBody();
		
		} elseif(strtolower($userMessage) == '/ashar')
		{
			$message = "Jadwal sholat ashar di Banjarmasin yaitu 15.51 WITA";
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
			$result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
			return $result->getHTTPStatus() . ' ' . $result->getRawBody();
		
		} elseif(strtolower($userMessage) == '/magrib')
		{
			$message = "Jadwal sholat magrib di Banjarmasin yaitu 18.42 WITA";
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
			$result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
			return $result->getHTTPStatus() . ' ' . $result->getRawBody();
		
		} elseif(strtolower($userMessage) == '/isya')
		{
			$message = "Jadwal sholat isya di Banjarmasin yaitu 19.53 WITA";
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
			$result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
			return $result->getHTTPStatus() . ' ' . $result->getRawBody();
		
		}  
		else {
			$message = "Silakan ketik /menu untuk melihat perintah";
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
			$result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
			return $result->getHTTPStatus() . ' ' . $result->getRawBody();
		}
	}
	

});

 $app->get('/push/{to}/{message}', function ($request, $response, $args)
 {
 	$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($_ENV['CHANNEL_ACCESS_TOKEN']);
 	$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $_ENV['CHANNEL_SECRET']]);

 	$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($args['message']);
 	$result = $bot->pushMessage($args['to'], $textMessageBuilder);

 	return $result->getHTTPStatus() . ' ' . $result->getRawBody();
 });

/* JUST RUN IT */
$app->run();