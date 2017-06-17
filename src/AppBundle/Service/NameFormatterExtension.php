<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 17/06/17
 * Time: 01:22
 */

namespace AppBundle\Service;

use AppBundle\Service\NameFormatter;

class NameFormatterExtension extends \Twig_Extension
{
    /**
     * @var NameFormatter
     */
    private $nameFormatter;

    public function __construct(NameFormatter $nameFormatter)
    {
        $this->nameFormatter = $nameFormatter;
    }

    public function format($text){
        return $this->nameFormatter->format($text);
    }

    public function getFunctions(){
        return array(
          new \Twig_SimpleFunction('formatName', array($this, "format")),
        );
    }

    public function getName(){
        return "NameFormatter";
    }

}