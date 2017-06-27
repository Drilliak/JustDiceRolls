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
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;


    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PlayerCharacter", inversedBy="characteristics")
     * @ORM\JoinColumn(nullable=true)
     */
    private $playerCharacter;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Game", inversedBy="characteristics")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;


    public function __construct($name)
    {
        $this->name = $name;
        $this->value = 0;
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
     * Set value
     *
     * @param float $value
     *
     * @return Characteristic
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
     * Set playerCharacter
     *
     * @param \AppBundle\Entity\PlayerCharacter $playerCharacter
     *
     * @return Characteristic
     */
    public function setPlayerCharacter(\AppBundle\Entity\PlayerCharacter $playerCharacter = null)
    {
        $this->playerCharacter = $playerCharacter;

        return $this;
    }

    /**
     * Get playerCharacter
     *
     * @return \AppBundle\Entity\PlayerCharacter
     */
    public function getPlayerCharacter()
    {
        return $this->playerCharacter;
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
