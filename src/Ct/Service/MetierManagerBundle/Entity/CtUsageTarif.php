<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtUsageTarif
 *
 * @ORM\Table(name="ct_usage_tarif", indexes={@ORM\Index(name="fk_ct_usage_tarif_ct_usage1_idx", columns={"ct_usage_id"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="uk_ct_usage_ct_usg_trf_annee_ct_type_visite", columns={"usg_trf_annee", "ct_usage_id", "ct_type_visite_id"})})
 * @ORM\Entity
 */
class CtUsageTarif
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
     * @ORM\Column(name="usg_trf_annee", type="string", length=4, nullable=true)
     */
    private $usgTrfAnnee;

    /**
     * @var float
     *
     * @ORM\Column(name="usg_trf_prix", type="float", precision=10, scale=0, nullable=true)
     */
    private $usgTrfPrix;


    /**
     * @var \CtUsage
     *
     * @ORM\ManyToOne(targetEntity="CtUsage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_usage_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $ctUsage;

    /**
     * @var \CtTypeVisite
     *
     * @ORM\ManyToOne(targetEntity="CtTypeVisite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_type_visite_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $ctTypeVisite;


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
     * Set usgTrfPrix
     *
     * @param float $usgTrfPrix
     *
     * @return CtUsageTarif
     */
    public function setUsgTrfPrix($usgTrfPrix)
    {
        $this->usgTrfPrix = $usgTrfPrix;

        return $this;
    }

    /**
     * Get usgTrfPrix
     *
     * @return float
     */
    public function getUsgTrfPrix()
    {
        return $this->usgTrfPrix;
    }


    /**
     * Set ctUsage
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtUsage $ctUsage
     *
     * @return CtUsageTarif
     */
    public function setCtUsage(\Ct\Service\MetierManagerBundle\Entity\CtUsage $ctUsage = null)
    {
        $this->ctUsage = $ctUsage;

        return $this;
    }

    /**
     * Get ctUsage
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtUsage
     */
    public function getCtUsage()
    {
        return $this->ctUsage;
    }

    /**
     * Set usgTrfAnnee
     *
     * @param string $usgTrfAnnee
     *
     * @return CtUsageTarif
     */
    public function setUsgTrfAnnee($usgTrfAnnee)
    {
        $this->usgTrfAnnee = $usgTrfAnnee;

        return $this;
    }

    /**
     * Get usgTrfAnnee
     *
     * @return string
     */
    public function getUsgTrfAnnee()
    {
        return $this->usgTrfAnnee;
    }

    /**
     * Set ctTypeVisite
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtTypeVisite $ctTypeVisite
     *
     * @return CtUsageTarif
     */
    public function setCtTypeVisite(\Ct\Service\MetierManagerBundle\Entity\CtTypeVisite $ctTypeVisite = null)
    {
        $this->ctTypeVisite = $ctTypeVisite;

        return $this;
    }

    /**
     * Get ctTypeVisite
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtTypeVisite
     */
    public function getCtTypeVisite()
    {
        return $this->ctTypeVisite;
    }
}
