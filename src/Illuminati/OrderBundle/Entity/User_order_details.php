<?php

namespace Illuminati\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User_order_details
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Illuminati\OrderBundle\Entity\User_order_detailsRepository")
 */
class User_order_details
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
     * @ORM\ManyToOne(targetEntity="Illuminati\UserBundle\Entity\User", inversedBy="orderDetails");
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="Illuminati\OrderBundle\Entity\Host_order", inversedBy="orderDetails")
     * @ORM\JoinColumn(name="host_order_id", referencedColumnName="id")
     */
    private $hostOrderId;

    //    /**
    //     * @ORM\ManyToOne(targetEntity="Illuminati\OrderBundle\Entity\User_order", inversedBy="orderDetails")
    //     * @ORM\JoinColumn(name="userOrderId", referencedColumnName="id")
    //     */
    //    private $userOrderId;

    /**
     * @ORM\ManyToOne(targetEntity="Illuminati\ProductBundle\Entity\Product", inversedBy="orderDetails")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $productId;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    //    /**
    //     * Set userOrderId
    //     *
    //     * @param integer $userOrderId
    //     *
    //     * @return User_order_details
    //     */
    //    public function setUserOrderId($userOrderId)
    //    {
    //        $this->userOrderId = $userOrderId;
    //
    //        return $this;
    //    }
    //
    //    /**
    //     * Get userOrderId
    //     *
    //     * @return integer
    //     */
    //    public function getUserOrderId()
    //    {
    //        return $this->userOrderId;
    //    }

    /**
     * Set productId
     *
     * @param integer $productId
     *
     * @return User_order_details
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return User_order_details
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return mixed
     */
    public function getHostOrderId()
    {
        return $this->hostOrderId;
    }

    /**
     * @param mixed $hostOrderId
     */
    public function setHostOrderId($hostOrderId)
    {
        $this->hostOrderId = $hostOrderId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }



}

