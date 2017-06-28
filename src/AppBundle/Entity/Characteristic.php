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
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * @var GameCharacteristic
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\GameCharacteristic")
     */
    private $gameCharacteristic;


    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PlayerCharacter", inversedBy="characteristics")
     * @ORM\JoinColumn(nullable=true)
     */
    private $playerCharacter;

    public function __construct()
    {
        $this->value = 0;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return $this->gameCharacteristic->getName();
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
     * Set gameCharacteristic
     *
     * @param \AppBundle\Entity\GameCharacteristic $gameCharacteristic
     *
     * @return Characteristic
     */
    public function setGameCharacteristic(\AppBundle\Entity\GameCharacteristic $gameCharacteristic = null)
    {
        $this->gameCharacteristic = $gameCharacteristic;

        return $this;
    }

    /**
     * Get gameCharacteristic
     *
     * @return \AppBundle\Entity\GameCharacteristic
     */
    public function getGameCharacteristic()
    {
        return $this->gameCharacteristic;
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
}
