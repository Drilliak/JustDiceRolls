<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Game
 *
 * @ORM\Table(name="games")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 */
class Game
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Characteristic", mappedBy="game", cascade={"persist"})
     */
    private $characteristics;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Statistic", mappedBy="game", cascade={"persist"})
     */
    private $statistics;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gameMaster;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Player", mappedBy="game")
     */
    private $players;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     * @ORM\Column(name="nb_spells_max", type="integer")
     */
    private $nbSpellsMax = 4;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->characteristics = new ArrayCollection();
        $this->players = new ArrayCollection();
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



    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getGameMaster(): User
    {
        return $this->gameMaster;
    }

    public function setGameMaster(User $gameMaster)
    {
        $this->gameMaster = $gameMaster;
    }


    /**
     * Add player
     *
     * @param \AppBundle\Entity\Player $player
     *
     * @return Game
     */
    public function addPlayer(\AppBundle\Entity\Player $player)
    {
        $this->players[] = $player;
        $player->setGame($this);

        return $this;
    }

    /**
     * Remove player
     *
     * @param \AppBundle\Entity\Player $player
     */
    public function removePlayer(\AppBundle\Entity\Player $player)
    {
        $this->players->removeElement($player);
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Game
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }



    /**
     * Set nbSpellsMax
     *
     * @param integer $nbSpellsMax
     *
     * @return Game
     */
    public function setNbSpellsMax($nbSpellsMax)
    {
        $this->nbSpellsMax = $nbSpellsMax;

        /** @var Player $player */
        foreach ($this->players as $player){
            $character = $player->getCharacter();
            if ($character != null){
                $character->setNbSpellsMax($nbSpellsMax);
                $player->setCharacter($character);
            }
        }
        return $this;
    }

    /**
     * Get nbSpellsMax
     *
     * @return integer
     */
    public function getNbSpellsMax()
    {
        return $this->nbSpellsMax;
    }

    /**
     * Add characteristic
     *
     * @param \AppBundle\Entity\Characteristic $characteristic
     *
     * @return Game
     */
    public function addCharacteristic(\AppBundle\Entity\Characteristic $characteristic)
    {
        $this->characteristics[] = $characteristic;
        $characteristic->setGame($this);
        return $this;
    }

    /**
     * Remove characteristic
     *
     * @param \AppBundle\Entity\Characteristic $characteristic
     */
    public function removeCharacteristic(\AppBundle\Entity\Characteristic $characteristic)
    {
        $this->characteristics->removeElement($characteristic);
    }

    /**
     * Get characteristics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCharacteristics()
    {
        return $this->characteristics;
    }

    /**
     * Add statistic
     *
     * @param \AppBundle\Entity\Statistic $statistic
     *
     * @return Game
     */
    public function addStatistic(\AppBundle\Entity\Statistic $statistic)
    {
        $this->statistics[] = $statistic;
        $statistic->setGame($this);
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
