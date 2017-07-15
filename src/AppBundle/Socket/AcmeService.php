<?php

namespace AppBundle\Socket;


use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use Ratchet\ConnectionInterface;

class AcmeService implements RpcInterface
{

    public function addFunc(ConnectionInterface $connection, WampRequest $request, $params){
        return array('result' => array_sum($params));
    }

    public function getName()
    {
        return "acme.rpc";
    }
}