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
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * @var float
     *
     * @ORM\Column(name="value_max", type="float", nullable=true)
     */
    private $valueMax;

    /**
     * @var GameStatistic
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\GameStatistic", inversedBy="statistics")
     */
    private $gameStatistic;

    /**
     * @var PlayerCharacter
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PlayerCharacter", inversedBy="statistics")
     */
    private $character;


    /**
     * Get name
     * @return string
     */
    public function getName() : string{
        return $this->getGameStatistic()->getName();
    }

    public function hasMax() : bool{
        return $this->getGameStatistic()->getHasMax();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param float $value
     *
     * @return Statistic
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * Set valueMax
     *
     * @param float $valueMax
     *
     * @return Statistic
     */
    public function setValueMax($valueMax)
    {
        $this->valueMax = $valueMax;

        return $this;
    }

    /**
     * Get valueMax
     *
     * @return float
     */
    public function getValueMax()
    {
        return $this->valueMax;
    }

    /**
     * Set gameStatistic
     *
     * @param \AppBundle\Entity\GameStatistic $gameStatistic
     *
     * @return Statistic
     */
    public function setGameStatistic(\AppBundle\Entity\GameStatistic $gameStatistic = null)
    {
        $this->gameStatistic = $gameStatistic;

        return $this;
    }

    /**
     * Get gameStatistic
     *
     * @return \AppBundle\Entity\GameStatistic
     */
    public function getGameStatistic()
    {
        return $this->gameStatistic;
    }

    /**
     * Set character
     *
     * @param \AppBundle\Entity\PlayerCharacter $character
     *
     * @return Statistic
     */
    public function setCharacter(\AppBundle\Entity\PlayerCharacter $character = null)
    {
        $this->character = $character;

        return $this;
    }

    /**
     * Get character
     *
     * @return \AppBundle\Entity\PlayerCharacter
     */
    public function getCharacter()
    {
        return $this->character;
    }
}
