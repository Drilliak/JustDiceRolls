<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Characteristic
 *
 * @ORM\Table(name="characteristic")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CharacteristicRepository")
 */
class Characteristic
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
     * @var bool
     *
     * @ORM\Column(name="hasMax", type="boolean")
     */
    private $hasMax;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Game", inversedBy="allowedCharacteristics")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;

    public function __construct($name, $hasMax = false)
    {
        $this->name = $name;
        $this->hasMax = $hasMax;
    }

    /**
     * Get id
     *
     * @return int
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
     * @return Characteristic
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
     * Set hasMax
     *
     * @param boolean $hasMax
     *
     * @return Characteristic
     */
    public function setHasMax($hasMax)
    {
        $this->hasMax = $hasMax;

        return $this;
    }

    /**
     * Get hasMax
     *
     * @return bool
     */
    public function getHasMax()
    {
        return $this->hasMax;
    }

    /**
     * Set game
     *
     * @param \AppBundle\Entity\Game $game
     *
     * @return Characteristic
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
}
