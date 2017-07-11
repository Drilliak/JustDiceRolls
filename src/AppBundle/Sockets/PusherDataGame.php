<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 06/07/2017
 * Time: 19:28
 */

namespace AppBundle\Sockets;


use Doctrine\Common\Collections\ArrayCollection;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class PusherDataGame implements WampServerInterface
{
    /**
     * @var ArrayCollection
     * Association id utilisateur en BDD avec l'objet <i>Connection</i> créé à l'initialisation du websocket.
     */
    private $usersToConns;


    public function __construct(){
        $this->usersToConns = new ArrayCollection();
    }

    function onOpen(ConnectionInterface $conn)
    {
        var_dump($conn->Session->get('_security_main'));
//        $idUser = unserialize($conn->Session->get('_security_main'))->getUser()->getId();
//        $this->usersToConns->set($idUser, $conn);

        echo "New connection! User $idUser with temp id connection {$conn->resourceId}\n";
    }

    function onClose(ConnectionInterface $conn)
    {
        $idUser = unserialize($conn->Session->get('_security_main'))->getUser()->getId();
        $this->usersToConns->remove($idUser);
        $conn->close();
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }

    function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        echo "Connection closed after a close try";
        $idUser = unserialize($conn->Session->get('_security_main'))->getUser()->getId();
        $this->usersToConns->remove($idUser);
        $conn->close();
    }

    function onSubscribe(ConnectionInterface $conn, $topic)
    {
        $idUser = unserialize($conn->Session->get('_security_main'))->getUser()->getId();
        $this->usersToConns->remove($idUser);
        $conn->close();
    }

    function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
        $idUser = unserialize($conn->Session->get('_security_main'))->getUser()->getId();
        $this->usersToConns->remove($idUser);
        $conn->close();
    }

    function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        echo "Connection closed after a publish try";
        $idUser = unserialize($conn->Session->get('_security_main'))->getUser()->getId();
        $this->usersToConns->remove($idUser);
        $conn->close();
    }

    public function sendData($entry){
        $entryData = json_decode($entry, true);
        /** @var ConnectionInterface $userToConn */
        foreach ($this->usersToConns as $userToConn){

            $userToConn->send($entryData);
        }
    }
}