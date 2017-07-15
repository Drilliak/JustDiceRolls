<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Game;
use AppBundle\Entity\Statistic;
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

        $userStats = [];

        /** @var Player $player */
        foreach ($game->getPlayers() as $player) {
            if ($player->getCharacter()) {
                $userStat = [];
                /** @var Statistic $statistic */
                foreach ($player->getCharacter()->getStatistics() as $statistic) {
                    $data = ['value' => $statistic->getValue(), 'id' => $statistic->getId()];
                    $userStat[$statistic->getName()] = $data;
                }

                $userStats[$player->getUser()->getUsername()] = $userStat;
            }
        }
        $this->get('acme.js_vars')->charData = [
            "ajaxPath" => $this->generateUrl("game_play_mj_ajax"),
            "gameId"   => $game->getId(),
        ];



        return $this->render("AppBundle:GamePlay:play_as_mj.html.twig", [
                "gameName"       => $game->getName(),
                "gameId"         => $game->getId(),
                "gameStatistics" => $game->getGameStatistics(),
                "players"        => $game->getPlayers(),
                "usersStats"     => $userStats,
            ]
        );
    }

    public function playAsMjAjaxAction(Request $request)
    {

        // if ($request->isXmlHttpRequest()){
        $action = strip_tags($request->get('action'));
        switch ($action) {
            case "change-stat-value":
                $value = (int)strip_tags($request->get('value'));
                $statId = (int)strip_tags($request->get('statId'));
                return $this->changeStatValue($statId, $value);
            case  "change-nb-spells":
                $value = (int)strip_tags($request->get('value'));
                $playerId = (int)strip_tags($request->get('playerId'));
                return $this->changeNbSpells($value, $playerId);
            case "change-max-stat-value":
                $maxValue = (int)strip_tags($request->get('maxValue'));
                $statId = (int)strip_tags($request->get('statId'));
                return $this->changeMaxStatValue($maxValue, $statId);
            case "change-characteristic":
                $value = (int)strip_tags($request->get('value'));
                $characId = (int)strip_tags($request->get('characId'));
                return $this->changeCharacteristic($value, $characId);
        }
        // }
        return new Response("This is not an AJAX request");
    }

    private function changeStatValue($statId, $value)
    {
        $em = $this->getDoctrine()->getManager();
        $statRepository = $em->getRepository('AppBundle:Statistic');
        $stat = $statRepository->find($statId);
        $stat->setValue($value);
        $em->flush();

        $name = $stat->getName();

        $pusher = $this->get('gos_web_socket.zmq.pusher');

        return new JsonResponse(['message' => "Statistic $name is now equal to $value."]);

    }

    private function changeNbSpells($value, $playerId)
    {
        $em = $this->getDoctrine()->getManager();
        $playerRepository = $em->getRepository('AppBundle:Player');
        $character = $playerRepository->find($playerId)->getCharacter();
        $character->setNbSpellsMax($value);
        $em->flush();
        $name = $character->getName();

        return new JsonResponse(['message' => "Character $name has now $value spells"]);
    }

    private function changeMaxStatValue($maxValue, $statId)
    {
        $em = $this->getDoctrine()->getManager();
        $statRepository = $em->getRepository('AppBundle:Statistic');
        $stat = $statRepository->find($statId);
        $stat->setValueMax($maxValue);
        $em->flush();

        $name = $stat->getName();
        return new JsonResponse(['message' => "Statistic $name max is now equal to $maxValue"]);
    }

    private function changeCharacteristic($value, $characId)
    {
        $em = $this->getDoctrine()->getManager();
        $characRepository = $em->getRepository('AppBundle:Characteristic');
        $characteristic = $characRepository->find($characId);
        $characteristic->setValue($value);
        $em->flush();

        $name = $characteristic->getName();
        return new JsonResponse(['message' => "Characteristic $name is now equal to $value."]);
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

        $otherPlayers = $playerRepository->findOtherPlayers($idGame, $player->getId());



        return $this->render('@App/GamePlay/play_as_player.html.twig', [
            'gameName'     => $game->getName(),
            'gameId'       => $game->getId(),
            'character'    => $player->getCharacter(),
            'otherPlayers' => $otherPlayers,
        ]);
    }
}
