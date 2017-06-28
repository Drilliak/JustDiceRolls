<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Characteristic;
use AppBundle\Entity\GameCharacteristic;
use AppBundle\Entity\GameStatistic;
use AppBundle\Entity\Player;
use AppBundle\Entity\PlayerCharacter;
use AppBundle\Entity\Statistic;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\PlayerCharacterType;



class GameController extends Controller
{


    public function createCharacterAction(Request $request, $idGame){

        $em = $this->getDoctrine()->getManager();
        $playerRepository = $em->getRepository('AppBundle:Player');
        $gameRepository = $em->getRepository('AppBundle:Game');

        $game = $gameRepository->find($idGame);

        $currentUserId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        /** @var Player $player */
        $player = $playerRepository->findPlayer($idGame, $currentUserId);

        if ($player == null) {
            $this->addFlash('danger', "Vous n'avez pas été invité à participer à cette partie.");
            return $this->redirectToRoute('homepage');
        }


        $playerCharacter = new PlayerCharacter();

        $gameCharacteristics = $game->getGameCharacteristics();

        /** @var GameCharacteristic $gameCharacteristic */
        foreach ($gameCharacteristics as $gameCharacteristic){
            $characteristic = new Characteristic();
            $characteristic->setGameCharacteristic($gameCharacteristic);
            $playerCharacter->addCharacteristic($characteristic);
        }

        $form = $this->createForm(PlayerCharacterType::class, $playerCharacter);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            /** @var UploadedFile $file */
            $file = $playerCharacter->getToken();

            // Génère un nom unique pour le fichier avant de l'enregistrer.
            $fileName = md5(uniqid()). '.' . $file->guessExtension();

            $file->move($this->getParameter('tokens_directory'), $fileName);

            $playerCharacter->setToken($fileName);

            $gameStatistics = $game->getGameStatistics();

            /** @var GameStatistic $gameStatistic */
            foreach ($gameStatistics as $gameStatistic){
                $statistic = new Statistic();
                $statistic->setGameStatistic($gameStatistic);
                $statistic->setValue(0);
                if ($gameStatistic->getHasMax()){
                    $statistic->setValueMax(0);
                }
                $playerCharacter->addStatistic($statistic);
            }


            $playerCharacter->setNbSpellsMax($game->getNbSpellsMax());

            $player->setCharacter($playerCharacter);

            $em->flush();

            $this->addFlash('info', "Votre personnage a bien été créé.");

            return $this->redirectToRoute('game_play_as_player', ['idGame' => $idGame]);


        }
        $this->addFlash('info', "Avant d'accéder à la partie, vous devez créer votre personnage.");

        return $this->render('AppBundle:Game:character_creation.html.twig', [
            'form'     => $form->createView(),
            'gameName' => $game->getName()
        ]);
    }

    public function showGamesAsPlayerAction(){
        $em = $this->getDoctrine()->getManager();

        $playerRepository = $em->getRepository('AppBundle:Player');

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $players = $playerRepository->findBy(['user' => $currentUser]);

        return $this->render('@App/Game/show_games_as_player.html.twig', ['players' => $players]);
    }

    public function showGamesAsGmAction(){
        $em = $this->getDoctrine()->getManager();
        $gameRepository = $em->getRepository('AppBundle:Game');
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $games = $gameRepository->findBy(['gameMaster' => $currentUser]);
        return $this->render('@App/Game/show_games_as_gm.html.twig', ['games' => $games]);
    }

}
