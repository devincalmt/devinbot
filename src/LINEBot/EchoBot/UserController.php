<?php


namespace LINE\LINEBot\EchoBot;


use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController
{
    private $view;
    private $logger;
    protected $table;

    public function index (Request $req, Response $res)
    {
        $res->getBody()->write(":DDD");
        return $res;
    }

    public function help(){

    }
}