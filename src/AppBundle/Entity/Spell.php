<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 20/06/17
 * Time: 19:59
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="spells")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SpellRepository")
 */
class Spell
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
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PlayerCharacter", inversedBy="spells")
     * @ORM\JoinColumn(nullable=true)
     */
    private $playerCharacter;



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
     * @return Spell
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
     * Set description
     *
     * @param string $description
     *
     * @return Spell
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
     * Set playerCharacter
     *
     * @param \AppBundle\Entity\PlayerCharacter $playerCharacter
     *
     * @return Spell
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
