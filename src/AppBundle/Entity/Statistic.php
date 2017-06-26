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
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", lenght=255)
     */
    private $name;

    /**
     * @var PlayerCharacter
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PlayerCharacter", inversedBy="statistics")
     * @ORM\JoinColumn(nullable=true)
     */
    private $playerCharacter;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_max", type="boolean")
     */
    private $hasMax;

    /**
     * @var float
     *
     * @ORM\Column(name="max_value", type="float", nullable=true)
     */
    private $maxValue;

}