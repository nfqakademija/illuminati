<?php

namespace Illuminati\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Order_participants
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Illuminati\OrderBundle\Entity\Order_participantsRepository")
 */
class Order_participants
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
     * @ORM\ManyToOne(targetEntity="Illuminati\OrderBundle\Entity\Host_order", inversedBy="order_participants")
     * @ORM\JoinColumn(name="host_order_id", referencedColumnName="id")
     */
    private $hostOrderId;

    /**
     * @var string
     *
     * @ORM\Column(name="users_email", type="string", length=255)
     */
    private $usersEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="invite_token", type="string", length=255)
     */
    private $inviteToken;

    /**
     * @var integer
     *
     * @ORM\Column(name="confirmed", type="smallint")
     */
    private $confirmed;

    /**
     * @var integer
     *
     * @ORM\Column(name="deleted", type="smallint")
     */
    private $deleted;

    public function __construct()
    {
        $this->deleted = 0;
        $this->confirmed = 0;
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
     * @return Order_participants
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
     * Set usersEmail
     *
     * @param string $usersEmail
     *
     * @return Order_participants
     */
    public function setUsersEmail($usersEmail)
    {
        $this->usersEmail = $usersEmail;

        return $this;
    }

    /**
     * Get usersEmail
     *
     * @return string
     */
    public function getUsersEmail()
    {
        return $this->usersEmail;
    }

    /**
     * Set inviteToken
     *
     * @param string $inviteToken
     *
     * @return Order_participants
     */
    public function setInviteToken($inviteToken)
    {
        $this->inviteToken = $inviteToken;

        return $this;
    }

    /**
     * Get inviteToken
     *
     * @return string
     */
    public function getInviteToken()
    {
        return $this->inviteToken;
    }

    /**
     * Set confirmed
     *
     * @param integer $confirmed
     *
     * @return Order_participants
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
     * Set deleted
     *
     * @param integer $deleted
     *
     * @return Order_participants
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
}

