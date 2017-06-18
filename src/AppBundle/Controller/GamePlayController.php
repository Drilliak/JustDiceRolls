<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Characteristic;
use AppBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Player;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


class GamePlayController extends Controller
{
    public function playAsMjAction($idGame)
    {
        $gameRepository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Game');

        /** @var Game $game */
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

        $allowedCharacteristics = [];
        /** @var Characteristic $allowedCharacteristic */
        foreach ($game->getAllowedCharacteristics() as $allowedCharacteristic) {
            if ($allowedCharacteristic->getHasMax()) {
                $allowedCharacteristics[] = $allowedCharacteristic->getName();
                $allowedCharacteristics[] = $allowedCharacteristic->getName() . " max";
            } else {
                $allowedCharacteristics[] = $allowedCharacteristic->getName();
            }
        }

        $players = [];

        $formatter = $this->get('app.name_formatter');
        /** @var Player $player */
        foreach ($game->getPlayers() as $player) {
            $playerData = [];
            $playerData['username'] = $player->getUser()->getUsername();

            if ($player->getCharacter() == null) {
                $playerData['playerName'] = 'N/A';
                $playerData['characteristics'] = [];
            } else {
                $playerData['playerName'] = $player->getCharacter()->getName();

                $characteristics = [];
                /** @var Characteristic $characteristic */
                foreach ($player->getCharacter()->getCharacteristics() as $characteristic) {

                    $characteristics[$formatter->format($characteristic->getName())] = $characteristic->getValue();
                    if ($characteristic->getHasMax()) {
                        $characteristics[$formatter->format($characteristic->getName() . 'max')] = $characteristic->getMaxValue();
                    }
                }
                $playerData['characteristics'] = $characteristics;
            }
            $playerData['id'] = $player->getId();

            $players[] = $playerData;
        }

        $this->get('acme.js_vars')->charData = [
            "allowedCharacteristics" => $allowedCharacteristics,
            "players" => $players,
            "ajaxPath" => $this->generateUrl("game_play_mj_ajax"),
            "gameId" => $game->getId(),
        ];

        return $this->render("AppBundle:GamePlay:play_as_mj.html.twig", [
                "gameName" => $game->getName(),
            ]
        );
    }

    public function playAsMjAjaxAction(Request $request)
    {
        // if ($request->isXmlHttpRequest()){
        $playerId = $request->get('playerId');
        $characteristicName = $request->get('characteristic');
        $newValue = $request->get('newValue');
        $gameId = $request->get('gameId');

        $em = $this->getDoctrine()->getManager();
        $playerRepository = $em->getRepository('AppBundle:Player');
        $player = $playerRepository->find(["id" => $playerId]);

        /** @var \AppBundle\Entity\PlayerCharacter $character */
        $character = $player->getCharacter();
        $characteristics = $character->getCharacteristics();

        $formatter = $this->get('app.name_formatter');
        /** @var Characteristic $characteristic */
        foreach ($characteristics as $characteristic) {
            if ($formatter->format($characteristic->getName()) == $characteristicName ||
                $formatter->format($characteristic->getName() . "max") == $characteristicName
            ) {
                $character->removeCharacteristic($characteristic);
                if (strpos($characteristicName, "max") === FALSE) {
                    $characteristic->setValue($newValue);
                } else {
                    $characteristic->setMaxValue($newValue);
                }
                $character->addCharacteristic($characteristic);
                $player->setCharacter($character);
                $em->flush();
                return new JsonResponse("success");
            }
        }
        return new JsonResponse("failed");
        // }
        return new Response("This is not an AJAX request");
    }

    public function playAsPlayerAction($idGame)
    {

        $playerRepository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Player');
        $gameRepository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Game');

        $game = $gameRepository->find($idGame);

        $currentUserId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $player = $playerRepository->findPlayer($idGame, $currentUserId);

        if ($player == null) {
            $this->addFlash('danger', "Vous n'avez pas été invité à participer à cette partie.");
            return $this->redirectToRoute('homepage');
        }

        if ($player->getCharacter() == null) {
            return $this->redirectToRoute('game_create_character', ['idGame' => $idGame]);
        }


        return $this->render('@App/GamePlay/play_as_player.html.twig', ['gameName' => $game->getName()]);
    }
}
