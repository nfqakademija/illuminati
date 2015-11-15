<?php

namespace Illuminati\OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User_order
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Illuminati\OrderBundle\Entity\User_orderRepository")
 */
class User_order
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Illuminati\OrderBundle\Entity\Host_order", inversedBy="user_orders")
     * @ORM\JoinColumn(name="host_order_id", referencedColumnName="id")
     */
    private $hostOrderId;

    /**
     * @ORM\ManyToOne(targetEntity="Illuminati\UserBundle\Entity\User", inversedBy="user_orders")
     * @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     */
    private $usersId;

    /**
     * @ORM\OneToMany(targetEntity="Illuminati\OrderBundle\Entity\User_order_details", mappedBy="userOrderId")
     */
    private $orderDetails;

    /**
     * @var integer
     *
     * @ORM\Column(name="confirmed", type="smallint")
     */
    private $confirmed;

    /**
     * @var integer
     *
     * @ORM\Column(name="payed", type="smallint")
     */
    private $payed;

    /**
     * @var integer
     *
     * @ORM\Column(name="deleted", type="smallint")
     */
    private $deleted;

    public function __construct()
    {
        $this->confirmed = 0;
        $this->payed = 0;
        $this->deleted = 0;
        $this->orderDetails = new ArrayCollection();
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
     * Set hostOrderId
     *
     * @param integer $hostOrderId
     *
     * @return User_order
     */
    public function setHostOrderId($hostOrderId)
    {
        $this->hostOrderId = $hostOrderId;

        return $this;
    }

    /**
     * Get hostOrderId
     *
     * @return integer
     */
    public function getHostOrderId()
    {
        return $this->hostOrderId;
    }

    /**
     * Set usersId
     *
     * @param integer $usersId
     *
     * @return User_order
     */
    public function setUsersId($usersId)
    {
        $this->usersId = $usersId;

        return $this;
    }

    /**
     * Get usersId
     *
     * @return integer
     */
    public function getUsersId()
    {
        return $this->usersId;
    }

    /**
     * Set confirmed
     *
     * @param integer $confirmed
     *
     * @return User_order
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get confirmed
     *
     * @return integer
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Set payed
     *
     * @param integer $payed
     *
     * @return User_order
     */
    public function setPayed($payed)
    {
        $this->payed = $payed;

        return $this;
    }

    /**
     * Get payed
     *
     * @return integer
     */
    public function getPayed()
    {
        return $this->payed;
    }

    /**
     * Set deleted
     *
     * @param integer $deleted
     *
     * @return User_order
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return integer
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @return mixed
     */
    public function getOrderDetails()
    {
        return $this->orderDetails;
    }


}

