<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
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
     * @var array
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\GameCharacteristic", mappedBy="game", cascade={"persist", "remove"})
     */
    private $gameCharacteristics;

    /**
     * @var array
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\GameStatistic", mappedBy="game", cascade={"persist", "remove"})
     */
    private $gameStatistics;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Add gameCharacteristic
     *
     * @param \AppBundle\Entity\GameCharacteristic $gameCharacteristic
     *
     * @return Game
     */
    public function addGameCharacteristic(\AppBundle\Entity\GameCharacteristic $gameCharacteristic)
    {
        $this->gameCharacteristics[] = $gameCharacteristic;
        $gameCharacteristic->setGame($this);
        return $this;
    }

    /**
     * Remove gameCharacteristic
     *
     * @param \AppBundle\Entity\GameCharacteristic $gameCharacteristic
     */
    public function removeGameCharacteristic(\AppBundle\Entity\GameCharacteristic $gameCharacteristic)
    {
        $this->gameCharacteristics->removeElement($gameCharacteristic);
        $gameCharacteristic->setGame(null);
    }

    /**
     * Get gameCharacteristics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGameCharacteristics()
    {
        return $this->gameCharacteristics;
    }

    /**
     * Add gameStatistic
     *
     * @param \AppBundle\Entity\GameStatistic $gameStatistic
     *
     * @return Game
     */
    public function addGameStatistic(\AppBundle\Entity\GameStatistic $gameStatistic)
    {
        $this->gameStatistics[] = $gameStatistic;
        $gameStatistic->setGame($this);
        return $this;
    }

    /**
     * Remove gameStatistic
     *
     * @param \AppBundle\Entity\GameStatistic $gameStatistic
     */
    public function removeGameStatistic(\AppBundle\Entity\GameStatistic $gameStatistic)
    {
        $this->gameStatistics->removeElement($gameStatistic);
        $gameStatistic->setGame(null);
    }

    /**
     * Get gameStatistics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGameStatistics()
    {
        return $this->gameStatistics;
    }
}
