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

use http\Exception;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

class Route
{
    private $db, $conn;

    public function register(App $app)
    {
        $app->get('/', 'UserController:index');

//        $app->post('/', 'UserController:help');
        $app->post('/', function (Request $req, Response $res) {
            $this->db = Connection::getInstance();
            $this->conn = $this->db->getConnection();

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

            $str = "";

            foreach ($events as $event) {
                if ($event.getSource() instanceof GroupSource || $event.getSource() instanceof RoomSource) {
                    if ($event->getText() == 'all') {
                        $member = $bot->getAllGroupMemberIds($event->getGroupId());
                        foreach ($member as $m){
                            $str .= $m . '\n';
                        }
                        $str .= $member;
                    }
                } else {
                    // from 1-on-1 chat
                }

                if ($event->getText() == 'help') {
                    $sql = "SELECT * FROM help";
                    $result = $this->conn->query($sql);

                    if ($result->num_rows > 0) {
                        // output data of each row
                        while ($row = $result->fetch_assoc()) {
                            $str .= ($row["id"] . " " . $row["name"] . "\n");
                        }
                    } else {
                        echo "0 results";
                    }
                }
                if ($event->getText() == 'ngambek') {
                    $this->replyStickerMessage($bot, $event->getReplyToken(), 11539,52114135);
                }

//                $replyText = $event->getText();
                $replyText = $str;
                $logger->info('Reply text: ' . $replyText);
                $resp = $bot->replyText($event->getReplyToken(), $replyText);
                $logger->info($resp->getHTTPStatus() . ': ' . $resp->getRawBody());
            }
        });
    }

    function replyStickerMessage($bot,$replyToken,$packageId,$stickerId) {
        $response = $bot->replyMessage($replyToken,new StickerMessageBuilder($packageId,$stickerId));
        if (!$response->isSucceeded()) {
            error_log('replyStickerMessage :' . $response->getHTTPStatus() . ' ' . $response->getRawBody());
        }
    }
}