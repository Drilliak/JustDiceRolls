<?php

namespace AppBundle\Command;

use AppBundle\Sockets\PusherDataGame;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Session\SessionProvider;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\ZMQ\Context;
use React\ZMQ\SocketWrapper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;


class SocketCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sockets:start')
            ->setHelp('Starts the chat socket demo')
            ->setDescription('Start the chat socket demo');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Chat socket',
            '============',
        ]);

        $container = $this->getContainer();
        $dbHost = $container->getParameter('database_host');
        $dbName = $container->getParameter('database_name');
        $dbUserName = $container->getParameter('database_user');
        $dbPassword = $container->getParameter('database_password');
        $dbPort = $container->getParameter('database_port');

        $sessionHandler = new PdoSessionHandler("mysql:host=$dbHost;dbname=$dbName;",
            ['db_username' => $dbUserName, 'db_password' => $dbPassword]);

        $loop = Factory::create();
        $pusher = new PusherDataGame();

        $socketAddr = $container->getParameter('app.socket_addr');
        $pusherPort = $container->getParameter('app.pusher_port');

        $context = new Context($loop);
        /** @var SocketWrapper $pull */
        $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
        $pull->bind("tcp://$socketAddr:$pusherPort"); // Binding to 127.0.0.1 means the only client that can connect is itself
        $pull->on('message', array($pusher, 'sendData'));


        $webSock = new Server($loop);
        $socketPort = $container->getParameter('app.socket_port');
        $webSock->listen($socketPort);

        $webServer = new IoServer(new HttpServer(new WsServer(new SessionProvider(new WampServer($pusher), $sessionHandler))), $webSock);


        $loop->run();
    }
}