<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtUsage
 *
 * @ORM\Table(name="ct_usage")
 * @ORM\Entity
 */
class CtUsage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="usg_libelle", type="string", length=255, nullable=true)
     */
    private $usgLibelle;

    /**
     * @var string
     *
     * @ORM\Column(name="usg_validite", type="string", length=255, nullable=true)
     */
    private $usgValidite;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="usg_created", type="datetime", nullable=true)
     */
    private $usgCreated;

    /**
     * @var \CtTypeUsage
     *
     * @ORM\ManyToOne(targetEntity="CtTypeUsage", inversedBy="ctUsage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_type_usage_id", referencedColumnName="id")
     * })
     */
    private $ctTypeUsage;


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
     * Set usgLibelle
     *
     * @param string $usgLibelle
     *
     * @return CtUsage
     */
    public function setUsgLibelle($usgLibelle)
    {
        $this->usgLibelle = $usgLibelle;

        return $this;
    }

    /**
     * Get usgLibelle
     *
     * @return string
     */
    public function getUsgLibelle()
    {
        return $this->usgLibelle;
    }

    /**
     * Set usgValidite
     *
     * @param string $usgValidite
     *
     * @return CtUsage
     */
    public function setUsgValidite($usgValidite)
    {
        $this->usgValidite = $usgValidite;

        return $this;
    }

    /**
     * Get usgValidite
     *
     * @return string
     */
    public function getUsgValidite()
    {
        return $this->usgValidite;
    }

    /**
     * Set usgCreated
     *
     * @param \DateTime $usgCreated
     *
     * @return CtUsage
     */
    public function setUsgCreated($usgCreated)
    {
        $this->usgCreated = $usgCreated;

        return $this;
    }

    /**
     * Get usgCreated
     *
     * @return \DateTime
     */
    public function getUsgCreated()
    {
        return $this->usgCreated;
    }

    /**
     * Set ctTypeUsage
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtTypeUsage $ctTypeUsage
     *
     * @return CtUsage
     */
    public function setCtTypeUsage(\Ct\Service\MetierManagerBundle\Entity\CtTypeUsage $ctTypeUsage = null)
    {
        $this->ctTypeUsage = $ctTypeUsage;

        return $this;
    }

    /**
     * Get ctTypeUsage
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtTypeUsage
     */
    public function getCtTypeUsage()
    {
        return $this->ctTypeUsage;
    }
}
