<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Characteristic;
use AppBundle\Entity\Game;
use AppBundle\Entity\Player;
use AppBundle\Entity\PlayerCharacter;
use AppBundle\Form\PlayerCharacterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class GameController extends Controller
{

    public function createNewGameAction(Request $request)
    {
        $gameName = $request->get('game-name');
        $pv = new Characteristic("PV", true);
        $mana = new Characteristic("Mana", true);
        $gameMaster = $this->get('security.token_storage')->getToken()->getUser();

        $game = new Game();
        $game->setName($gameName);
        $game->addAllowedCharacteristic($pv);
        $game->addAllowedCharacteristic($mana);
        $game->setGameMaster($gameMaster);

        $em = $this->getDoctrine()->getManager();
        $em->persist($game);
        $em->flush();
        $idGame = $game->getId();
        $this->addFlash('info', 'Nouvelle partie créée, vous pouvez désormais la personnaliser');

        return $this->redirectToRoute('game_edition', ['idGame' => $idGame]);

    }

    public function playAsMjAction($idGame)
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

        return $this->render("AppBundle:Game:play_as_mj.html.twig", [
                "gameName" => $game->getName(),
            ]
        );
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


        return $this->render('@App/Game/play_as_player.html.twig', ['gameName' => $game->getName()]);
    }

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

        $form = $this->get('form.factory')->create(PlayerCharacterType::class, $playerCharacter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            /** @var UploadedFile $file */
            $file = $playerCharacter->getToken();

            // Génère un nom unique pour le fichier avant de l'enregistrer.
            $fileName = md5(uniqid()). '.' . $file->guessExtension();

            $file->move($this->getParameter('tokens_directory'), $fileName);

            $playerCharacter->setToken($fileName);

            $allowedCharacteristics = $game->getAllowedCharacteristics();
            /** @var Characteristic $allowedCharacteristic */
            foreach($allowedCharacteristics as $allowedCharacteristic){
                if ($allowedCharacteristic->getHasMax()){
                    $playerCharacter->addCharacteristic($allowedCharacteristic->getName(),0,true);
                } else {
                    $playerCharacter->addCharacteristic($allowedCharacteristic->getName());
                }
            }

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
