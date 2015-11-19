<?php

namespace Illuminati\OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Host_order
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Illuminati\OrderBundle\Entity\Host_orderRepository")
 */
class Host_order
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
     * @ORM\ManyToOne(targetEntity="Illuminati\UserBundle\Entity\User", inversedBy="hosted_orders")
     * @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     */
    private $usersId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank(message="notBlank.title")
     * @Assert\Length(
     *      min="3",
     *      max="255",
     *      minMessage="min.Title",
     *      maxMessage="max.Title"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank(message="notBlank.description")
     * @Assert\Length(
     *      min="5",
     *      minMessage="min.Description"
     * )
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="close_date", type="datetime")
     */
    private $closeDate;

        /**
         * @var integer
         * @ORM\Column(name="state_id", type="smallint")
         */
        private $stateId;

    /**
     * @var integer
     *
     * @ORM\Column(name="deleted", type="smallint")
     */
    private $deleted;

    //    /**
    //     * @ORM\OneToMany(targetEntity="Illuminati\OrderBundle\Entity\Order_participants", mappedBy="hostOrderId")
    //     */
    //    private $order_participants;

    /**
     * @var string
     *
     * @ORM\Column(name="host_order_token", type="string", length=32, unique=true)
     */
    private $host_order_token;

    /**
     * @ORM\OneToMany(targetEntity="Illuminati\OrderBundle\Entity\User_order", mappedBy="hostOrderId")
     */
    private $user_orders;

    /**
     * @ORM\ManyToOne(targetEntity="Illuminati\ProductBundle\Entity\Supplier", inversedBy="host_order_id")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    private $supplier_id;

    public function __construct()
    {
        $this->stateId = 1;
        $this->host_order_token = hex2bin(openssl_random_pseudo_bytes(32));
        $this->deleted = 0;
        //$this->order_participants = new ArrayCollection();
        $this->user_orders = new ArrayCollection();
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
     * Set usersId
     *
     * @param integer $usersId
     *
     * @return Host_order
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
     * Set title
     *
     * @param string $title
     *
     * @return Host_order
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Host_order
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
     * Set closeDate
     *
     * @param \DateTime $closeDate
     *
     * @return Host_order
     */
    public function setCloseDate($closeDate)
    {
        $this->closeDate = $closeDate;

        return $this;
    }

    /**
     * Get closeDate
     *
     * @return \DateTime
     */
    public function getCloseDate()
    {
        return $this->closeDate;
    }

    /**
     * Set stateId
     *
     * @param integer $stateId
     *
     * @return Host_order
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;

        return $this;
    }

    /**
     * Get stateId
     *
     * @return integer
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * Set deleted
     *
     * @param integer $deleted
     *
     * @return Host_order
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

    //    /**
    //     * Get Order Participants
    //     *
    //     * @return mixed
    //     */
    //    public function getOrderParticipants()
    //    {
    //        return $this->order_participants;
    //    }

    /**
     * @return mixed
     */
    public function getUserOrders()
    {
        return $this->user_orders;
    }

    /**
     * @return mixed
     */
    public function getSupplierId()
    {
        return $this->supplier_id;
    }

    /**
     * @param mixed $supplier_id
     */
    public function setSupplierId($supplier_id)
    {
        $this->supplier_id = $supplier_id;
    }

    /**
     * @return string
     */
    public function getHostOrderToken()
    {
        return $this->host_order_token;
    }

}
