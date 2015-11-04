<?php

namespace Illuminati\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="deleted", type="smallint", options={ "default" = 0 })
     */
    protected $deleted;

    /**
     * @ORM\Column(name="name", type="string", length=50)
     * @Assert\NotBlank(message="name.notBlank", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="name.tooShort",
     *     maxMessage="name.tooLong",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $name;

    /**
     * @ORM\Column(name="surname", type="string", length=50)
     * @Assert\NotBlank(message="surname.notBlank", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="surname.tooShort",
     *     maxMessage="surname.tooLong",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $surname;

    /**
     * @ORM\OneToMany(targetEntity="Illuminati\OrderBundle\Entity\Host_order", mappedBy="usersId")
     */
    protected $hosted_orders;

    public function __construct()
    {
        parent::__construct();
        $this->deleted = 0;
        $this->hosted_orders = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return int
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param int $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getHostedOrders()
    {
        return $this->hosted_orders;
    }


}