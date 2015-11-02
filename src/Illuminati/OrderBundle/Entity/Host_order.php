<?php

namespace Illuminati\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var integer
     *
     * @ORM\Column(name="users_id", type="integer")
     */
    private $usersId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="close_date", type="datetime")
     */
    private $closeDate;

    /**
     * @ORM\ManyToOne(targetEntity="Illuminati\OrderBundle\Entity\Host_order_state", inversedBy="host_orders")
     * @ORM\JoinColumn(name="State_ID", referencedColumnName="id")
     */
    private $stateId;

    /**
     * @var integer
     *
     * @ORM\Column(name="deleted", type="smallint")
     */
    private $deleted;

    public function __construct()
    {
        $this->deleted = 0;
        $this->stateId = 1; //Opened by default;
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
}
