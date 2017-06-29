<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Characteristic;
use AppBundle\Entity\Game;
use AppBundle\Entity\PlayerCharacter;
use AppBundle\Entity\Statistic;
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

        $userStats = [];

        /** @var Player $player */
        foreach ($game->getPlayers() as $player) {
            if ($player->getCharacter()){
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
                $value = (int) strip_tags($request->get('value'));
                $statId = (int) strip_tags($request->get('statId'));
                return $this->changeStatValue($statId, $value);
        }
        // }
        return new Response("This is not an AJAX request");
    }

    private function changeStatValue($statId, $value){
        $em = $this->getDoctrine()->getManager();
        $statRepository = $em->getRepository('AppBundle:Statistic');
        $stat = $statRepository->find($statId);
        $stat->setValue($value);
        $em->flush();

        $name = $stat->getName();
        return new JsonResponse(['message' => "Statistic $name is now equal to $value."]);

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
