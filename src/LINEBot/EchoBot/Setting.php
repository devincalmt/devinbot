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

class Setting
{
    public static function getSetting()
    {
        return [
            'settings' => [
                'displayErrorDetails' => true, // set to false in production

                'logger' => [
                    'name' => 'slim-app',
                    'path' => __DIR__ . '/../../../logs/app.log',
                ],

                'bot' => [
                    'channelToken' => 'G7+gXh9GAHlvjWuTOIM/LuDi5Qb4uzZllHkxUA8wULhnYkJtb9W64zomfgFiReF/VhcrQ9EGUpEwesnfSpqimlayfy5iVjdclFkLhfrlnqUmzc478dBVtoWsRcIiTLa8Wrx1JHEYWvi7he58hTdqNwdB04t89/1O/w1cDnyilFU=',
                    'channelSecret' => 'bec4d1db205ee33572c3f0321203947b',
                ],

                'apiEndpointBase' => getenv('LINEBOT_API_ENDPOINT_BASE'),

                'db' => [
                    'host' => 'us-cdbr-iron-east-04.cleardb.net',
                    'database' => 'heroku_fdb27654ad74a1b',
                    'username' => 'b1f3fa9bda05bb',
                    'password' => '10d0741f',
                    'charset'   => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix'    => '',
                ]
            ],
        ];
    }
}
