<?php

namespace AppBundle\Service;

class NameFormatter{

    public function format($text){
        return strtolower(str_replace(" ", "", $text));
    }
}