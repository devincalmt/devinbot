<?php
require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//use Slim\Factory\AppFactory;

use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;

$pass_signature = true;

// set LINE channel_access_token and channel_secret
$channel_access_token = "6PCsA2dmn0JEc1bsAzBSxGJaJjHF3cRwVRSgGBzaxXrp+Wbp3saU/D0bXYRvrF2rVhcrQ9EGUpEwesnfSpqimlayfy5iVjdclFkLhfrlnqU9ToKDYEATtBKJGMCYiOwOUyXJlw2PKe+rPWAcNVyXXwdB04t89/1O/w1cDnyilFU=";
$channel_secret = "bec4d1db205ee33572c3f0321203947b";

// inisiasi objek bot
$httpClient = new CurlHTTPClient($channel_access_token);
$bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);

$configs =  [
    'settings' => ['displayErrorDetails' => true],
];
$app = new Slim\App($configs);
//$app->setBasePath("/public");

$app->get('/', function (Request $request, Response $response, $args) {
//    $response->getBody()->write("Hello World!");
//    return $response;
    return "aaa";
});

// buat route untuk webhook
$app->post('/webhook', function (Request $request, Response $response) use ($channel_secret, $bot, $httpClient, $pass_signature) {
    // get request body and line signature header
    $body = $request->getBody();
    $signature = $request->getHeaderLine('HTTP_X_LINE_SIGNATURE');

    // log body and signature
    file_put_contents('php://stderr', 'Body: ' . $body);

    if ($pass_signature === false) {
        // is LINE_SIGNATURE exists in request header?
        if (empty($signature)) {
            return $response->withStatus(400, 'Signature not set');
        }

        // is this request comes from LINE?
        if (!SignatureValidator::validateSignature($body, $channel_secret, $signature)) {
            return $response->withStatus(400, 'Invalid signature');
        }
    }

// kode aplikasi nanti disini
    $data = json_decode($body, true);
    if(is_array($data['events'])){
        foreach ($data['events'] as $event)
        {
            if ($event['type'] == 'message')
            {
                if($event['message']['type'] == 'text')
                {
                    // send same message as reply to user
                    $result = $bot->replyText($event['replyToken'], $event['message']['text']);


                    // or we can use replyMessage() instead to send reply message
                    // $textMessageBuilder = new TextMessageBuilder($event['message']['text']);
                    // $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);


                    $response->getBody()->write((string)$result->getJSONDecodedBody());
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus($result->getHTTPStatus());
                }
            }
        }
    }
});
$app->run();