<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 27/06/2017
 * Time: 19:59
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class GameCharacteristic
 *
 * @package AppBundle\Entity
 *
 * @ORM\Table(name="game_characteristics")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameCharacteristicRepository")
 */
class GameCharacteristic
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
     * @return GameCharacteristic
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
}
