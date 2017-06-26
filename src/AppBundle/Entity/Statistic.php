<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 25/06/2017
 * Time: 20:01
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Statistic
 *
 * @package AppBundle\Entity
 * @ORM\Table(name="statistics")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StatisticRepository")
 */
class Statistic
{
    private $id;

    private $name;

    private $playerCharacter;

    private $value;

    private $hasMax;

    private $maxValue;

}