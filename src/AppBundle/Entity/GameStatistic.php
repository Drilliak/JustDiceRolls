<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 28/06/2017
 * Time: 18:31
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class GameStatistic
 * @package AppBundle\Entity
 *
 * @ORM\Table(name="game_statistics")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameStatisticRepository")
 */
class GameStatistic
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_max", type="boolean")
     */
    private $hasMax;

    /**
     * @var Game
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Game", inversedBy="gameStatistics")
     */
    private $game;

    /**
     * @var array
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Statistic", mappedBy="gameStatistic")
     */
    private $statistics;

    public function __construct($name, bool $hasMax = false)
    {
        $this->statistics = new ArrayCollection();
        $this->name = $name;
        $this->hasMax = $hasMax;
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
     * Set name
     *
     * @param string $name
     *
     * @return GameStatistic
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set game
     *
     * @param \AppBundle\Entity\Game $game
     *
     * @return GameStatistic
     */
    public function setGame(\AppBundle\Entity\Game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \AppBundle\Entity\Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set hasMax
     *
     * @param boolean $hasMax
     *
     * @return GameStatistic
     */
    public function setHasMax($hasMax)
    {
        $this->hasMax = $hasMax;

        return $this;
    }

    /**
     * Get hasMax
     *
     * @return boolean
     */
    public function getHasMax()
    {
        return $this->hasMax;
    }

    /**
     * Add statistic
     *
     * @param \AppBundle\Entity\Statistic $statistic
     *
     * @return GameStatistic
     */
    public function addStatistic(\AppBundle\Entity\Statistic $statistic)
    {
        $this->statistics[] = $statistic;

        return $this;
    }

    /**
     * Remove statistic
     *
     * @param \AppBundle\Entity\Statistic $statistic
     */
    public function removeStatistic(\AppBundle\Entity\Statistic $statistic)
    {
        $this->statistics->removeElement($statistic);
    }

    /**
     * Get statistics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStatistics()
    {
        return $this->statistics;
    }
}
