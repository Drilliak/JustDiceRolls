<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 21/06/2017
 * Time: 16:20
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CharacterEditorController extends Controller
{
    public function editCharacterAction(Request $request, $idGame)
    {

        $em = $this->getDoctrine()->getManager();
        $playerRepository = $em->getRepository('AppBundle:Player');
        $idUser = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $player = $playerRepository->findPlayer($idGame, $idUser);
        if ($player === null) {
            $this->addFlash('danger', "Vous n'avez pas été invité à participer à cette partie.");
            return $this->redirectToRoute('homepage');
        }

        $gameRepository = $em->getRepository('AppBundle:Game');
        $game = $gameRepository->find($idGame);

        return $this->render("AppBundle:CharacterEditor:character_edit.html.twig", [
                'gameName'  => $game->getName(),
                'character' => $player->getCharacter(),

            ]
        );
    }
}