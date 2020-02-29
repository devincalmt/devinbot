<?php


namespace LINE\LINEBot\EchoBot;


use Slim\Http\Request;
use Slim\Http\Response;

class UserController
{
    public static function index (Request $req, Response $res)
    {
        $res->getBody()->write(":DDD");
        return $res;
    }
}