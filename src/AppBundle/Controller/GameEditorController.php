<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Characteristic;
use AppBundle\Entity\Player;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UserBundle\UserBundle;

class GameEditorController extends Controller
{
    public function editAction($idGame)
    {
        $gameRepository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Game');
        $game = $gameRepository->find($idGame);

        // Si aucune game n'est trouvée, on redirige vers la page d'accueil
        // avec un message spécifiant le problème.
        if ($game == null) {
            $this->addFlash('warning', "Vous avez tenté d'accéder à la page d'une partie inexistance");
            return $this->redirectToRoute('homepage');
        }

        $currentUserId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $gameMasterId = $this->getDoctrine()->getManager()->getRepository('AppBundle:Game')->find($idGame)->getGameMaster()->getId();

        // Si un utilisateur autre que le maître du jeu essaie d'accéder à la page
        // on le redirige avec un message spécifiant le problème
        if ($currentUserId != $gameMasterId) {
            $this->addFlash('danger', "Vous ne possédez pas les droits nécessaires pour accéder à cette page");
            return $this->redirectToRoute('homepage');
        }

        $this->get('acme.js_vars')->charData = [
            "idGame"           => $idGame,
            "ajaxPath"         => $this->generateUrl('game_edition_ajax'),
            "autocompletePath" => $this->generateUrl('game_edition_autocomplete')
        ];


        return $this->render("AppBundle:GameEditor:game_edit.html.twig", [
                "gameName"        => $game->getName(),
                "characteristics" => $game->getAllowedCharacteristics(),
                "idGame"          => $idGame,
                "players"         => $game->getPlayers()->toArray()
            ]
        );
    }

    /**
     * Méthode permettant de recharger dynamiquement la page /ajax/edition-partie
     */
    public function editAjaxAction(Request $request)
    {
        // if ($request->isXmlHttpRequest()) {
        $currentUserId = $this->get('security.token_storage')->getToken()->getUser()->getId();

        $idGame = $request->get('idGame');
        $gameMasterId = $this->getDoctrine()->getManager()->getRepository('AppBundle:Game')->find($idGame)->getGameMaster()->getId();

        if ($currentUserId != $gameMasterId) {
            return new Response('Invalid user');
        }

        $action = $request->get('action');
        switch ($action) {
            case "add-characteristic":
                return $this->addCharacteristic($idGame, $request->get('newCharacteristicName'));
            case "change-has-max":
                return $this->changeHasMax($idGame, trim($request->get('characteristicName')), $request->get('newHasMax'));
            case "remove-characteristic":
                $hasMax = (trim($request->get('hasMax') == "Oui")) ? true : false;
                return $this->removeCharacteristic($idGame, trim($request->get('characteristicName')), $hasMax);
            case "add-player":
                $playerName = $request->get('playerName');
                return $this->addPlayer($idGame, $playerName);
            case "remove-player":
                $playerName = $request->get('playerName');
                return $this->removePlayer($idGame, $playerName);
        }
        //  }

        return new Response('This is not an ajax request.');
    }

    private function addCharacteristic($idGame, $newCharacteristicName)
    {
        $gameRepository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Game');
        $game = $gameRepository->find($idGame);

        $em = $this->getDoctrine()->getManager();

        $characteristic = new Characteristic($newCharacteristicName);
        $game->addAllowedCharacteristic($characteristic);
        $em->flush();

        return new JsonResponse('success');
    }

    private function changeHasMax($idGame, $characteristicName, $newHasMax)
    {
        $em = $this->getDoctrine()->getManager();
        $gameRepository = $em->getRepository('AppBundle:Game');
        $game = $gameRepository->find($idGame);

        /**
         * @var ArrayCollection
         */
        $characteristics = $game->getAllowedCharacteristics();
        foreach ($characteristics as $characteristic) {
            if ($characteristic->getName() == $characteristicName) {
                $oldCharacteristic = $characteristic;
                $newHasMax = ($newHasMax == 'Oui');
                $characteristic->setHasMax($newHasMax);
                $game->addAllowedCharacteristic($characteristic);
                $game->removeAllowedCharacteristic($oldCharacteristic);
                $em->flush();

                return new JsonResponse("changed");
            }
        }

        return new JsonResponse("Not changed");
    }

    private function removeCharacteristic($idGame, string $characteristicName, bool $hasMax)
    {
        $em = $this->getDoctrine()->getManager();
        $gameRepository = $em->getRepository('AppBundle:Game');
        $game = $gameRepository->find($idGame);

        $characteristics = $game->getAllowedCharacteristics();
        foreach ($characteristics as $characteristic) {
            if ($characteristic->getName() == $characteristicName && $characteristic->getHasMax() == $hasMax) {
                $game->removeAllowedCharacteristic($characteristic);
                $em->remove($characteristic);
                $em->flush();
                return new JsonResponse("changed");
            }
        }

        return new JsonResponse("success");
    }

    private function addPlayer($idGame, $playerName)
    {
        $em = $this->getDoctrine()->getManager();
        $gameRepository = $em->getRepository('AppBundle:Game');
        $userRepository = $em->getRepository('UserBundle:User');
        $game = $gameRepository->find($idGame);
        $user = $userRepository->findOneBy(['username' => $playerName]);

        $player = new Player();
        $player->setGame($game);
        $player->setUser($user);

        $em->persist($player);

        $em->flush();
        return new JsonResponse("player added");
    }

    private function removePlayer($idGame, $playerName){
        $em = $this->getDoctrine()->getManager();

        $gameRepository = $em->getRepository('AppBundle:Game');
        $userRepository = $em->getRepository('UserBundle:User');

        $game = $gameRepository->find($idGame);
        $player = $userRepository->findOneBy(['username' => $playerName]);

        $game->removePlayer($player);
        $em->flush();

        return new JsonResponse('player removed');
    }

    public function autocompleteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $term = $request->get('term');
            $userRepository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $sqlRes = $userRepository->autocomplete($term, $this->get('security.token_storage')->getToken()->getUsername());
            $autocompleteRes = [];

            foreach ($sqlRes as $val) {
                $autocompleteRes[] = $val["username"];
            }

            return new JsonResponse($autocompleteRes);
        }
        return new Response('Not an AJAX request', 400);

    }
}
