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
    public function editCharacterAction(Request $request, $idGame){

        return $this->render("AppBundle:CharacterEditor:character_edit.html.twig");
    }
}