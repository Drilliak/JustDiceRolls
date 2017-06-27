<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Player;
use AppBundle\Entity\Statistic;
use AppBundle\Entity\Characteristic;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


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
            "idGame" => $idGame,
        ];


        return $this->render("AppBundle:GameEditor:game_edit.html.twig", [
                "gameName"        => $game->getName(),
                "idGame"          => $idGame,
                "nbSpellsMax"     => $game->getNbSpellsMax(),
                "players"         => $game->getPlayers(),
                "statistics"      => $game->getStatistics(),
                "characteristics" => $game->getCharacteristics()
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

        $idGame = (int) strip_tags($request->get('idGame'));
        $gameMasterId = $this->getDoctrine()->getManager()->getRepository('AppBundle:Game')->find($idGame)->getGameMaster()->getId();

        if ($currentUserId != $gameMasterId) {
            return new JsonResponse('Invalid user');
        }

        $action = strip_tags($request->get('action'));
        switch ($action) {
            case "add-statistic":
                $stat = $request->get('stat');
                return $this->addStatistic($idGame, $stat);
            case "remove-stat":
                $statId = $request->get('statId');
                return $this->removeStat($idGame, $statId);
            case "add-characteristic":
                $characteristic = $request->get('characteristic');
                return $this->addCharacteristic($idGame, $characteristic);
            case "add-player":
                $playerName = $request->get('playerName');
                return $this->addPlayer($idGame, $playerName);
            case "remove-player":
                $playerName = $request->get('playerName');
                return $this->removePlayer($idGame, $playerName);
            case "change-nb-spells-max":
                $value = $request->get('value');
                return $this->changeNbSpellsMax($idGame, $value);
        }
        //  }

        return new Response('This is not an ajax request.');
    }


    private function addStatistic($idGame, $statName){
        $statName = trim(strip_tags($statName));
        $em = $this->getDoctrine()->getManager();
        $gameRepository = $em->getRepository('AppBundle:Game');
        $playerRepository = $em->getRepository('AppBundle:Player');

        $game = $gameRepository->find($idGame);
        $players = $playerRepository->findPlayers($idGame);

        $stat = new Statistic($statName);

        $game->addStatistic($stat);
        /** @var Player $player */
        foreach ($players as $player){
            $character = $player->getCharacter();
            if ($character !== null){
                $stat->setHasMax(false);
                $stat->setValue(0);
                $character->addStatistic($stat);
                $player->setCharacter($character);
            }
        }
        $em->flush();

        return new JsonResponse(['id' => $stat->getId()]);

    }

    private function removeStat($idGame, $statId){
        $statId = (int) strip_tags($statId);
        $em = $this->getDoctrine()->getManager();
        $gameRepository = $em->getRepository('AppBundle:Game');
        $playerRepository = $em->getRepository('AppBundle:Player');
        $statRepository = $em->getRepository('AppBundle:Statistic');

        $game = $gameRepository->find($idGame);
        $players = $playerRepository->findPlayers($idGame);
        $stat = $statRepository->find($statId);

        $game->removeStatistic($stat);
        /** @var Player $player */
        foreach($players as $player){
            $character = $player->getCharacter();
            if ($character !== null){
                /** @var Statistic $statistic */
                foreach($character->getStatistics() as $statistic){
                    if (strcasecmp($statistic->getName(), $stat->getName()) == 0){
                        $character->removeStatistic($statistic);
                        $player->setCharacter($character);
                        break;
                    }
                }
            }
        }
        $em->remove($stat);
        $em->flush();

        $name = $stat->getName();
        return new JsonResponse("Statistic $name removed");

    }

    private function addCharacteristic($idGame, $characteristicName){
        $characteristicName= trim(strip_tags($characteristicName));

        $em = $this->getDoctrine()->getManager();
        $gameRepository = $em->getRepository('AppBundle:Game');
        $playerRepository = $em->getRepository('AppBundle:Player');

        $game = $gameRepository->find($idGame);
        $players = $playerRepository->findPlayers($idGame);

        $characteristic = new Characteristic($characteristicName);

        $game->addCharacteristic($characteristic);

        /** @var Player $player */
        foreach ($players as $player){
            $character = $player->getCharacter();
            if ($character !== null){
                $characteristic->setValue(0);
                $character->addCharacteristic($characteristic);
                $player->setCharacter($character);
            }
        }
        $em->flush();
        return new JsonResponse(['id' => $characteristic->getId()]);

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

    private function removePlayer($idGame, $playerName)
    {
        $em = $this->getDoctrine()->getManager();

        $gameRepository = $em->getRepository('AppBundle:Game');
        $userRepository = $em->getRepository('UserBundle:User');

        $game = $gameRepository->find($idGame);
        $player = $userRepository->findOneBy(['username' => $playerName]);

        $game->removePlayer($player);
        $em->flush();

        return new JsonResponse('player removed');
    }

    private function changeNbSpellsMax($idGame, $value)
    {
        $value = (int)trim($value);
        $em = $this->getDoctrine()->getManager();
        $gameRepository = $em->getRepository('AppBundle:Game');
        $game = $gameRepository->find($idGame);
        $game->setNbSpellsMax($value);
        $em->flush();

        return new JsonResponse("Number of spells sucessfully changed.");
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
