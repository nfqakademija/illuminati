<?php

namespace Illuminati\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Host_order_state
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Illuminati\OrderBundle\Entity\Host_order_stateRepository")
 */
class Host_order_state
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
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=30)
     */
    private $state;

    /**
     * @ORM\OneToMany(targetEntity="Illuminati\OrderBundle\Entity\Host_order", mappedBy="stateId")
     */
    private $host_orders;


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
     * Set state
     *
     * @param string $state
     *
     * @return Host_order_state
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }
}

