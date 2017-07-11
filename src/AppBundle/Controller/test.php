<?php

require dirname(dirname(dirname(__DIR__))) . "/vendor/autoload.php";

$loop = \React\EventLoop\Factory::create();

$pusher = new \AppBundle\Sockets\PusherDataGame();

$context = new \React\ZMQ\Context($loop);

$pull = $context->getSocket(\ZMQ::SOCKET_PULL);
$pull->bind('tcp://127.0.0.1:5555');