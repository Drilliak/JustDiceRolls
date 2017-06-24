<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 23/06/2017
 * Time: 18:05
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CharacterController extends Controller
{
    public function showCharactersAction(){
        $currentUser =  $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $playerRepository = $em->getRepository('AppBundle:Player');

        $players = $playerRepository->findBy(['user' => $currentUser]);

        return $this->render("@App/Character/show_my_characters.twig", ['players' =>$players]);
    }
}