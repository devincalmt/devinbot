<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace LINE\LINEBot\EchoBot;

use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use LINE\LINEBot\EchoBot\UserController;

class Route
{
    public function register(App $app)
    {
        $app->get('/', 'UserController:index');
//        $app->get('/', function (Request $req, Response $res){
////            $res->getBody()->write(":DD");
////            return $res;
//            $servername = "us-cdbr-iron-east-04.cleardb.net";
//            $username = "b1f3fa9bda05bb";
//            $password = "10d0741f";
//            $dbname = "heroku_fdb27654ad74a1b";
//
//// Create connection
//            $conn = mysqli_connect($servername, $username, $password, $dbname);
//
//// Check connection
//            if (!$conn) {
//                die("Connection failed: " . mysqli_connect_error());
//            }
//            echo "Connected successfully";
//
//            $this->test();
//        });

        $app->post('/', function (Request $req, Response $res) {
            /** @var \LINE\LINEBot $bot */
            $bot = $this->bot;
            /** @var \Monolog\Logger $logger */
            $logger = $this->logger;

            $signature = $req->getHeader(HTTPHeader::LINE_SIGNATURE);
            if (empty($signature)) {
                return $res->withStatus(400, 'Bad Request');
            }

            // Check request with signature and parse request
            try {
                $events = $bot->parseEventRequest($req->getBody(), $signature[0]);
            } catch (InvalidSignatureException $e) {
                return $res->withStatus(400, 'Invalid signature');
            } catch (InvalidEventRequestException $e) {
                return $res->withStatus(400, "Invalid event request");
            }

            foreach ($events as $event) {
                if (!($event instanceof MessageEvent)) {
                    $logger->info('Non message event has come');
                    continue;
                }

                if (!($event instanceof TextMessage)) {
                    $logger->info('Non text message has come');
                    continue;
                }

                $replyText = $event->getText();
                $logger->info('Reply text: ' . $replyText);
                $resp = $bot->replyText($event->getReplyToken(), $replyText);
                $logger->info($resp->getHTTPStatus() . ': ' . $resp->getRawBody());
            }

            $res->write('OK');
            return $res;
        });
    }

    public function test(){
        echo 'a';
    }
}