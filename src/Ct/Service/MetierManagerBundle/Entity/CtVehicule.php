<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtVehicule
 *
 * @ORM\Table(name="ct_vehicule", indexes={@ORM\Index(name="fk_ct_vehicule_ct_marque1_idx", columns={"ct_marque_id"}), @ORM\Index(name="fk_ct_vehicule_ct_genre1_idx", columns={"ct_genre_id"})})
 * @ORM\Entity
 */
class CtVehicule
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
     * @ORM\Column(name="vhc_cylindre", type="string", length=10, nullable=true)
     */
    private $vhcCylindre;

    /**
     * @var float
     *
     * @ORM\Column(name="vhc_puissance", type="float", precision=10, scale=0, nullable=true)
     */
    private $vhcPuissance;

    /**
     * @var float
     *
     * @ORM\Column(name="vhc_poids_vide", type="float", precision=10, scale=0, nullable=true)
     */
    private $vhcPoidsVide;

    /**
     * @var float
     *
     * @ORM\Column(name="vhc_charge_utile", type="float", precision=10, scale=0, nullable=true)
     */
    private $vhcChargeUtile;

    /**
     * @var float
     *
     * @ORM\Column(name="vhc_poids_total_charge", type="float", precision=10, scale=0, nullable=true)
     */
    private $vhcPoidsTotalCharge;

    /**
     * @var float
     *
     * @ORM\Column(name="vhc_hauteur", type="float", precision=10, scale=0, nullable=true)
     */
    private $vhcHauteur;

    /**
     * @var float
     *
     * @ORM\Column(name="vhc_largeur", type="float", precision=10, scale=0, nullable=true)
     */
    private $vhcLargeur;

    /**
     * @var float
     *
     * @ORM\Column(name="vhc_longueur", type="float", precision=10, scale=0, nullable=true)
     */
    private $vhcLongueur;

    /**
     * @var string
     *
     * @ORM\Column(name="vhc_num_serie", type="string", length=100, nullable=true)
     */
    private $vhcNumSerie;

    /**
     * @var string
     *
     * @ORM\Column(name="vhc_num_moteur", type="string", length=100, nullable=true)
     */
    private $vhcNumMoteur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="vhc_created", type="datetime", nullable=true)
     */
    private $vhcCreated;

    /**
     * @var string
     *
     * @ORM\Column(name="vhc_provenance", type="string", length=45, nullable=true)
     */
    private $vhcProvenance;

    /**
     * @var string
     *
     * @ORM\Column(name="vhc_type", type="string", length=45, nullable=true)
     */
    private $vhcType;

    /**
     * @var \CtGenre
     *
     * @ORM\ManyToOne(targetEntity="CtGenre")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_genre_id", referencedColumnName="id")
     * })
     */
    private $ctGenre;

    /**
     * @var \CtMarque
     *
     * @ORM\ManyToOne(targetEntity="CtMarque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_marque_id", referencedColumnName="id")
     * })
     */
    private $ctMarque;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->vhcCreated = new \DateTime();
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
     * Set vhcCylindre
     *
     * @param string $vhcCylindre
     *
     * @return CtVehicule
     */
    public function setVhcCylindre($vhcCylindre)
    {
        $this->vhcCylindre = $vhcCylindre;

        return $this;
    }

    /**
     * Get vhcCylindre
     *
     * @return string
     */
    public function getVhcCylindre()
    {
        return $this->vhcCylindre;
    }

    /**
     * Set vhcPuissance
     *
     * @param float $vhcPuissance
     *
     * @return CtVehicule
     */
    public function setVhcPuissance($vhcPuissance)
    {
        $this->vhcPuissance = $vhcPuissance;

        return $this;
    }

    /**
     * Get vhcPuissance
     *
     * @return float
     */
    public function getVhcPuissance()
    {
        return $this->vhcPuissance;
    }

    /**
     * Set vhcPoidsVide
     *
     * @param float $vhcPoidsVide
     *
     * @return CtVehicule
     */
    public function setVhcPoidsVide($vhcPoidsVide)
    {
        $this->vhcPoidsVide = $vhcPoidsVide;

        return $this;
    }

    /**
     * Get vhcPoidsVide
     *
     * @return float
     */
    public function getVhcPoidsVide()
    {
        return $this->vhcPoidsVide;
    }

    /**
     * Set vhcChargeUtile
     *
     * @param float $vhcChargeUtile
     *
     * @return CtVehicule
     */
    public function setVhcChargeUtile($vhcChargeUtile)
    {
        $this->vhcChargeUtile = $vhcChargeUtile;

        return $this;
    }

    /**
     * Get vhcChargeUtile
     *
     * @return float
     */
    public function getVhcChargeUtile()
    {
        return $this->vhcChargeUtile;
    }

    /**
     * Set vhcHauteur
     *
     * @param float $vhcHauteur
     *
     * @return CtVehicule
     */
    public function setVhcHauteur($vhcHauteur)
    {
        $this->vhcHauteur = $vhcHauteur;

        return $this;
    }

    /**
     * Get vhcHauteur
     *
     * @return float
     */
    public function getVhcHauteur()
    {
        return $this->vhcHauteur;
    }

    /**
     * Set vhcLargeur
     *
     * @param float $vhcLargeur
     *
     * @return CtVehicule
     */
    public function setVhcLargeur($vhcLargeur)
    {
        $this->vhcLargeur = $vhcLargeur;

        return $this;
    }

    /**
     * Get vhcLargeur
     *
     * @return float
     */
    public function getVhcLargeur()
    {
        return $this->vhcLargeur;
    }

    /**
     * Set vhcLongueur
     *
     * @param float $vhcLongueur
     *
     * @return CtVehicule
     */
    public function setVhcLongueur($vhcLongueur)
    {
        $this->vhcLongueur = $vhcLongueur;

        return $this;
    }

    /**
     * Get vhcLongueur
     *
     * @return float
     */
    public function getVhcLongueur()
    {
        return $this->vhcLongueur;
    }

    /**
     * Set vhcNumSerie
     *
     * @param string $vhcNumSerie
     *
     * @return CtVehicule
     */
    public function setVhcNumSerie($vhcNumSerie)
    {
        $this->vhcNumSerie = $vhcNumSerie;

        return $this;
    }

    /**
     * Get vhcNumSerie
     *
     * @return string
     */
    public function getVhcNumSerie()
    {
        return $this->vhcNumSerie;
    }

    /**
     * Set vhcNumMoteur
     *
     * @param string $vhcNumMoteur
     *
     * @return CtVehicule
     */
    public function setVhcNumMoteur($vhcNumMoteur)
    {
        $this->vhcNumMoteur = $vhcNumMoteur;

        return $this;
    }

    /**
     * Get vhcNumMoteur
     *
     * @return string
     */
    public function getVhcNumMoteur()
    {
        return $this->vhcNumMoteur;
    }

    /**
     * Set vhcCreated
     *
     * @param \DateTime $vhcCreated
     *
     * @return CtVehicule
     */
    public function setVhcCreated($vhcCreated)
    {
        $this->vhcCreated = $vhcCreated;

        return $this;
    }

    /**
     * Get vhcCreated
     *
     * @return \DateTime
     */
    public function getVhcCreated()
    {
        return $this->vhcCreated;
    }

    /**
     * Set vhcProvenance
     *
     * @param string $vhcProvenance
     *
     * @return CtVehicule
     */
    public function setVhcProvenance($vhcProvenance)
    {
        $this->vhcProvenance = $vhcProvenance;

        return $this;
    }

    /**
     * Get vhcProvenance
     *
     * @return string
     */
    public function getVhcProvenance()
    {
        return $this->vhcProvenance;
    }

    /**
     * Set ctGenre
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtGenre $ctGenre
     *
     * @return CtVehicule
     */
    public function setCtGenre(\Ct\Service\MetierManagerBundle\Entity\CtGenre $ctGenre = null)
    {
        $this->ctGenre = $ctGenre;

        return $this;
    }

    /**
     * Get ctGenre
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtGenre
     */
    public function getCtGenre()
    {
        return $this->ctGenre;
    }

    /**
     * Set ctMarque
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtMarque $ctMarque
     *
     * @return CtVehicule
     */
    public function setCtMarque(\Ct\Service\MetierManagerBundle\Entity\CtMarque $ctMarque = null)
    {
        $this->ctMarque = $ctMarque;

        return $this;
    }

    /**
     * Get ctMarque
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtMarque
     */
    public function getCtMarque()
    {
        return $this->ctMarque;
    }

    /**
     * Set vhcType
     *
     * @param string $vhcType
     *
     * @return CtVehicule
     */
    public function setVhcType($vhcType)
    {
        $this->vhcType = $vhcType;

        return $this;
    }

    /**
     * Get vhcType
     *
     * @return string
     */
    public function getVhcType()
    {
        return $this->vhcType;
    }

    /**
     * Set vhcPoidsTotalCharge
     *
     * @param float $vhcPoidsTotalCharge
     *
     * @return CtVehicule
     */
    public function setVhcPoidsTotalCharge($vhcPoidsTotalCharge)
    {
        $this->vhcPoidsTotalCharge = $vhcPoidsTotalCharge;

        return $this;
    }

    /**
     * Get vhcPoidsTotalCharge
     *
     * @return float
     */
    public function getVhcPoidsTotalCharge()
    {
        return $this->vhcPoidsTotalCharge;
    }
}
