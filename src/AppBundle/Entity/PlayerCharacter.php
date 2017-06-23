<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Exception\PlayerChracterException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PlayerCharacter
 *
 * @ORM\Table(name="player_character")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerCharacterRepository")
 */
class PlayerCharacter
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
     * @var string
     *
     * @ORM\Column(name="backStory", type="text", nullable=true)
     */
    private $backStory;

    /**
     * @var array
     *
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Characteristic", mappedBy="playerCharacter")
     * @ORM\OrderBy({"name" = "ASC" })
     *
     */
    private $characteristics;

    /**
     * @ORM\Column(type="string")
     * @Assert\File(
     *     mimeTypes={"image/png"},
     *     mimeTypesMessage="Veuillez choisir une image au format png."
     * )
     */
    private $token;

    /**
     * @var int
     * @ORM\Column(name="nb_spells_max", type="integer")
     */
    private $nbSpellsMax;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Spell", mappedBy="playerCharacter")
     */
    private $spells;


    public function __construct()
    {
        $this->characteristics = new ArrayCollection();
        $this->spells = new ArrayCollection();
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
     * @return PlayerCharacter
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
     * Set backStory
     *
     * @param string $backStory
     *
     * @return PlayerCharacter
     */
    public function setBackStory($backStory)
    {
        $this->backStory = $backStory;

        return $this;
    }

    /**
     * Get backStory
     *
     * @return string
     */
    public function getBackStory()
    {
        return $this->backStory;
    }



    /**
     * Set token
     *
     * @param string $token
     *
     * @return PlayerCharacter
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set characteristics
     *
     * @param string $characteristics
     *
     * @return PlayerCharacter
     */
    public function setCharacteristics($characteristics)
    {
        $this->characteristics = $characteristics;

        return $this;
    }

    /**
     * Get characteristics
     *
     * @return array
     */
    public function getCharacteristics()
    {
        return $this->characteristics;
    }

    /**
     * Add characteristic
     *
     * @param \AppBundle\Entity\Characteristic $characteristic
     *
     * @return PlayerCharacter
     */
    public function addCharacteristic(\AppBundle\Entity\Characteristic $characteristic)
    {
        $this->characteristics[] = $characteristic;
        $characteristic->setPlayerCharacter($this);
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
     * Add spell
     *
     * @param \AppBundle\Entity\Spell $spell
     *
     * @return bool
     */
    public function addSpell(\AppBundle\Entity\Spell $spell)
    {
        if ($this->spells->count() == $this->nbSpellsMax){
            return  false;
        }
        $this->spells[] = $spell;
        $spell->setPlayerCharacter($this);
        return true;
    }

    /**
     * Remove spell
     *
     * @param \AppBundle\Entity\Spell $spell
     */
    public function removeSpell(\AppBundle\Entity\Spell $spell)
    {
        $this->spells->removeElement($spell);
    }

    /**
     * Get spells
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSpells()
    {
        return $this->spells;
    }

    /**
     * Set nbSpells
     *
     * @param integer $nbSpells
     *
     * @return PlayerCharacter
     */
    public function setNbSpells($nbSpells)
    {
        $this->nbSpells = $nbSpells;

        return $this;
    }

    /**
     * Get nbSpells
     *
     * @return integer
     */
    public function getNbSpells()
    {
        return $this->nbSpells;
    }

    /**
     * Set nbSpellsMax
     *
     * @param integer $nbSpellsMax
     *
     * @return PlayerCharacter
     */
    public function setNbSpellsMax($nbSpellsMax)
    {
        $this->nbSpellsMax = $nbSpellsMax;

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
}
