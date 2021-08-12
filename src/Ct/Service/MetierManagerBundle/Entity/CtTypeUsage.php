<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeUsage
 *
 * @ORM\Table(name="ct_type_usage")
 * @ORM\Entity
 */
class CtTypeUsage
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
     * @ORM\Column(name="tpu_libelle", type="string", length=45)
     */
    private $tpuLibelle;

    /**
     * @var \CtUsage
     *
     * @ORM\OneToMany(targetEntity="CtUsage", mappedBy="ctTypeUsage")
     */
    private $ctUsage;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tpuLibelle
     *
     * @param string $tpuLibelle
     *
     * @return TypeUsage
     */
    public function setTpuLibelle($tpuLibelle)
    {
        $this->tpuLibelle = $tpuLibelle;

        return $this;
    }

    /**
     * Get tpuLibelle
     *
     * @return string
     */
    public function getTpuLibelle()
    {
        return $this->tpuLibelle;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ctUsage = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add ctUsage
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtUsage $ctUsage
     *
     * @return CtTypeUsage
     */
    public function addCtUsage(\Ct\Service\MetierManagerBundle\Entity\CtUsage $ctUsage)
    {
        $this->ctUsage[] = $ctUsage;

        return $this;
    }

    /**
     * Remove ctUsage
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtUsage $ctUsage
     */
    public function removeCtUsage(\Ct\Service\MetierManagerBundle\Entity\CtUsage $ctUsage)
    {
        $this->ctUsage->removeElement($ctUsage);
    }

    /**
     * Get ctUsage
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCtUsage()
    {
        return $this->ctUsage;
    }
}
