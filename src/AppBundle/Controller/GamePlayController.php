<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Characteristic;
use AppBundle\Entity\Game;
use AppBundle\Entity\PlayerCharacter;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
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
        $idPlayers = [];

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
            $idPlayers[] = $player->getId();
            $playerData['character'] = $player->getCharacter();

            $players[] = $playerData;
        }

        $this->get('acme.js_vars')->charData = [
            "ajaxPath"               => $this->generateUrl("game_play_mj_ajax"),
            "gameId"                 => $game->getId(),
            "idPlayers"              => $idPlayers,
            "allowedCharacteristics" => $allowedCharacteristics,
        ];

        return $this->render("AppBundle:GamePlay:play_as_mj.html.twig", [
                "players"                => $players,
                "allowedCharacteristics" => $allowedCharacteristics,
                "gameName"               => $game->getName(),
                "gameId"                 => $game->getId(),
            ]
        );
    }

    public function playAsMjAjaxAction(Request $request)
    {

        // if ($request->isXmlHttpRequest()){
        $action = $request->get('action');
        switch ($action) {
            case "update-stat":
                $playerId = $request->get('playerId');
                $characteristicName = $request->get('characteristic');
                $newValue = $request->get('newValue');
                return $this->updateStat($playerId, $characteristicName, $newValue);
            case "get-data-character":
                $idPlayer = $request->get('playerId');
                return $this->getDataCharacter($idPlayer);
        }
        // }
        return new Response("This is not an AJAX request");
    }

    private function updateStat($playerId, $characteristicName, $newValue)
    {

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
    }

    private function getDataCharacter($idPlayer)
    {
        $playerRepository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Player');

        /** @var Player $player */
        $player = $playerRepository->find($idPlayer);

        /** @var PlayerCharacter $character */
        $character = $player->getCharacter();
        $tokenPath = $this->get('assets.packages')->getUrl('img/tokens/') . $character->getToken();
        /** @var CacheManager $imagineCacheManager */
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $path = $imagineCacheManager->getBrowserPath('img/tokens/' . $player->getCharacter()->getToken(), "thumb_token_filter");

        $characteristics = [];
        /** @var Characteristic $characteristic */
        foreach ($character->getCharacteristics() as $characteristic) {
            $values = [];
            if ($characteristic->getHasMax()) {
                $values['value'] = $characteristic->getValue();
                $values['max'] = $characteristic->getMaxValue();
            } else {
                $values['value'] = $characteristic->getValue();
            }
            $characteristics[$characteristic->getName()] = $values;
        }
        $res = [
            "tokenPath"       => $path,
            "characteristics" => $characteristics
        ];
        return new JsonResponse($res);
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


        return $this->render('@App/GamePlay/play_as_player.html.twig', [
            'gameName' => $game->getName(),
            'gameId'   => $game->getId(),
        ]);
    }
}
