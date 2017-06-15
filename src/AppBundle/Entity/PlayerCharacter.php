<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Exception\PlayerChracterException;
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
     * @ORM\Column(name="characteristics", type="array")
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
     * Set characteristics
     *
     * @param array $characteristics
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

    public function addCharacteristic($name, $value = 0, $hasMax = false){
        if ($hasMax){
            $this->characteristics[$name] = $value;
        } else {
            $this->characteristics[$name] = $value;
            $this->characteristics[$name . 'Max'] = $value;
        }
    }

    /**
     * Affecte une valeur à une caractéristiques en fonction de son nom
     * @param $name
     * @param $value
     * @throws PlayerChracterException
     */
    public function setCharacteristic($name, $value){
        if (!array_key_exists($name, $this->characteristics)){
            throw new PlayerChracterException("The given key is not set in the array.");
        }
        $this->characteristics[$name] = $value;
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
}
