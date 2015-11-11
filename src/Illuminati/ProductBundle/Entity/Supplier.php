<?php

namespace Illuminati\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Supplier
 *
 * @ORM\Table(name="supplier", uniqueConstraints={@ORM\UniqueConstraint(name="name_UNIQUE", columns={"name"})})
 * @ORM\Entity
 */
class Supplier
{
    /**
     * @var string
     *
     * @ORM\Column(name="provider_class", type="string", length=255, nullable=false)
     */
    private $provider_class;

    /**
     * @return string
     */
    public function getProviderClass()
    {
        return $this->provider_class;
    }

    /**
     * @param string $provider_class
     */
    public function setProviderClass($provider_class)
    {
        $this->provider_class = $provider_class;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return Supplier
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
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Supplier
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
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
}
