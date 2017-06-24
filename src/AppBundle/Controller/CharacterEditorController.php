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
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Spell;

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

        if ($player->getCharacter() == null) {
            $this->addFlash('info', "Vous devez d'abord créé votre personnage.");
            return $this->redirectToRoute('game_create_character');
        }

        $gameRepository = $em->getRepository('AppBundle:Game');
        $game = $gameRepository->find($idGame);

        $this->get('acme.js_vars')->charData = [
            "ajaxPath"          => $this->generateUrl('edit_character_ajax'),
            "idUser"            => $this->get('security.token_storage')->getToken()->getUser()->getId(),
            "idPlayerCharacter" => $player->getCharacter()->getId(),
            "nbSpellsMax"       => $player->getCharacter()->getNbSpellsMax()
        ];

        return $this->render("AppBundle:CharacterEditor:character_edit.html.twig", [
                'gameName'  => $game->getName(),
                'character' => $player->getCharacter(),

            ]
        );
    }

    public function editCharacterAjaxAction(Request $request)
    {
        // if ($request->isXmlHttpRequest()) {
        $idPlyaer = $request->get('idUser');

        if ($this->get('security.token_storage')->getToken()->getUser()->getId() != $idPlyaer) {
            return new JsonResponse('Invalid user');
        }
        $action = $request->get('action');

        switch ($action) {
            case "add-new-spell":
                $idPlayerCharacter = $request->get('idPlayerCharacter');
                $name = $request->get('spellName');
                $description = $request->get('spellDescription');
                return $this->addNewSpell($idPlayerCharacter, $name, $description);
        }

        return new JsonResponse('failed');
        // }

    }

    public function addNewSpell($idPlayerCharacter, $name, $description)
    {
        $em = $this->getDoctrine()->getManager();
        $playerCharacterRepository = $em->getRepository('AppBundle:PlayerCharacter');

        $character = $playerCharacterRepository->find($idPlayerCharacter);
        $spell = new Spell();

        $spell->setName($name);
        $spell->setDescription($description);
        if ($character->addSpell($spell)) {
            $em->persist($spell);
        }
        $em->flush();
        return new JsonResponse([$name, $description]);
    }
}