<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GameCharacteristic;
use AppBundle\Entity\GameStatistic;
use AppBundle\Form\GameType;
use AppBundle\Sockets\Chat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Game;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{

    public function indexAction(Request $request)
    {

        $game = new Game();
        /** @var Form $form */
        $form = $this->get('form.factory')->create(GameType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gameMaster = $this->get('security.token_storage')->getToken()->getUser();
            $game->setGameMaster($gameMaster);

            $social = new GameCharacteristic("Social");
            $intelligence = new GameCharacteristic("Intelligence");
            $force = new GameCharacteristic("Force");

            $game->addGameCharacteristic($social);
            $game->addGameCharacteristic($intelligence);
            $game->addGameCharacteristic($force);

            $pv = new GameStatistic("PV", true);
            $mana = new GameStatistic("Mana", true);
            $level = new GameStatistic("Niveau");

            $game->addGameStatistic($pv);
            $game->addGameStatistic($mana);
            $game->addGameStatistic($level);

            $em = $this->getDoctrine()->getManager();
            $em->persist($game);

            $em->flush();

            return $this->redirectToRoute('game_edition', ['idGame' => $game->getId()]);
        }

        return $this->render("AppBundle:Home:index.html.twig", ['form' => $form->createView()]);
    }

    public function testAction()
    {

    }
}
