<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 12/07/2017
 * Time: 22:57
 */

namespace AppBundle\Socket;


use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientStorageInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Symfony\Bridge\Doctrine\RegistryInterface;
use UserBundle\Entity\User;

class GamePlayTopic implements TopicInterface, PushableTopicInterface
{

    /**
     * @var ClientManipulatorInterface
     */
    protected $clientManipulator;

    /** @var  RegistryInterface */
    protected $registry;

    public function __construct(ClientManipulatorInterface $clientManipulator, RegistryInterface $registry)
    {
        $this->clientManipulator = $clientManipulator;
        $this->registry = $registry;
    }

    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        // Si le joueur n'envoie pas les bons attributs par le socket, c'est qu'il a modifié le code js
        // On le déconnecte
        $attributes = $request->getAttributes();
        if (!$attributes->has('idGame') || !$attributes->has('username')){
            $connection->close();
            return;
        }

        $userNameSent = $attributes->get('username');
        $user = $this->clientManipulator->getClient($connection);

        // Si le nom envoyé est différent de celui récupéré en BDD grâce à la session, c'est que l'utilisateur
        // a modifié le code JS, on le déconnecte.
        if ($userNameSent !== $user->getUsername()){
            $connection->close();
            return;
        }

        $repository = $this->registry->getManager()->getRepository('AppBundle:Game');

        /**
         * @var boolean $isPlayerGame TRUE si le joueur faisant la requête est un joueur de la partie
         *                            FALSE sinon
         */
        $isPlayerGame = $repository->hasUser($attributes->get('idGame'), $user->getUsername());

        // Si le joueur ne fait pas partie de la partie, on le déconnecte
        if (!$isPlayerGame){
            $connection->close();
            return;
        }

        $topic->broadcast(['msg' => $user . " has joined " . $topic->getId()]);
    }

    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $topic->broadcast(['msg' => $connection->resourceId . " has left " . $topic->getId()]);
    }

    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        // Le joueur n'a pas le droit de publier (seul le MJ peut le faire via une requête AJAX).
        // Si un joueur tente de publier, c'est qu'il tente de hacker à travers la console, on le
        // déconnecte donc immédiatement.

        $connection->close();
    }

    public function getName()
    {
        return 'gameplay.topic';
    }

    public function onPush(Topic $topic, WampRequest $request, $data, $provider)
    {

        $topic->broadcast(['msg' => $data]);
    }
}