<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtVisiteExtraTarif
 *
 * @ORM\Table(name="ct_visite_extra_tarif", indexes={@ORM\Index(name="fk_ct_visite_extra_tarif_ct_visite_extra1_idx", columns={"ct_visite_extra_id"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="uk_ct_visite_extra_ct_vet_annee", columns={"vet_annee", "ct_visite_extra_id"})})
 * @ORM\Entity
 */
class CtVisiteExtraTarif
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
     * @ORM\Column(name="vet_annee", type="string", length=4, nullable=true)
     */
    private $vetAnnee;

    /**
     * @var float
     *
     * @ORM\Column(name="vet_prix", type="float", precision=10, scale=0, nullable=true)
     */
    private $vetPrix;

    /**
     * @var \CtVisiteExtra
     *
     * @ORM\ManyToOne(targetEntity="CtVisiteExtra")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_visite_extra_id", referencedColumnName="id")
     * })
     */
    private $ctVisiteExtra;



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
     * Set vetAnnee
     *
     * @param string $vetAnnee
     *
     * @return CtVisiteExtraTarif
     */
    public function setVetAnnee($vetAnnee)
    {
        $this->vetAnnee = $vetAnnee;

        return $this;
    }

    /**
     * Get vetAnnee
     *
     * @return string
     */
    public function getVetAnnee()
    {
        return $this->vetAnnee;
    }

    /**
     * Set vetPrix
     *
     * @param float $vetPrix
     *
     * @return CtVisiteExtraTarif
     */
    public function setVetPrix($vetPrix)
    {
        $this->vetPrix = $vetPrix;

        return $this;
    }

    /**
     * Get vetPrix
     *
     * @return float
     */
    public function getVetPrix()
    {
        return $this->vetPrix;
    }

    /**
     * Set ctVisiteExtra
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtVisiteExtra $ctVisiteExtra
     *
     * @return CtVisiteExtraTarif
     */
    public function setCtVisiteExtra(\Ct\Service\MetierManagerBundle\Entity\CtVisiteExtra $ctVisiteExtra = null)
    {
        $this->ctVisiteExtra = $ctVisiteExtra;

        return $this;
    }

    /**
     * Get ctVisiteExtra
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtVisiteExtra
     */
    public function getCtVisiteExtra()
    {
        return $this->ctVisiteExtra;
    }
}
