<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtConstAvDedCarac
 *
 * @ORM\Table(name="ct_const_av_ded_carac", indexes={
 *     @ORM\Index(name="fk_ct_const_av_ded_carac_ct_genre1_idx", columns={"ct_genre_id"}),
 *     @ORM\Index(name="fk_ct_const_av_ded_carac_ct_marque1_idx", columns={"ct_marque_id"}),
 *     @ORM\Index(name="fk_ct_const_av_ded_carac_ct_source_energie1_idx", columns={"ct_source_energie_id"}),
 *     @ORM\Index(name="fk_ct_const_av_ded_carac_ct_carosserie1_idx", columns={"ct_carosserie_id"}),
 *     @ORM\Index(name="fk_ct_const_av_ded_carac_ct_const_av_ded_type1_idx", columns={"ct_const_av_ded_type_id"})
 * })
 * @ORM\Entity
 */
class CtConstAvDedCarac
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
     * @ORM\Column(name="cad_cylindre", type="string", length=10, nullable=true)
     */
    private $cadCylindre;

    /**
     * @var float
     *
     * @ORM\Column(name="cad_puissance", type="float", precision=10, scale=0, nullable=true)
     */
    private $cadPuissance;

    /**
     * @var float
     *
     * @ORM\Column(name="cad_poids_vide", type="float", precision=10, scale=0, nullable=true)
     */
    private $cadPoidsVide;

    /**
     * @var float
     *
     * @ORM\Column(name="cad_charge_utile", type="float", precision=10, scale=0, nullable=true)
     */
    private $cadChargeUtile;

    /**
     * @var float
     *
     * @ORM\Column(name="cad_poids_total_charge", type="float", precision=10, scale=0, nullable=true)
     */
    private $cadPoidsTotalCharge;

    /**
     * @var float
     *
     * @ORM\Column(name="cad_hauteur", type="float", precision=10, scale=0, nullable=true)
     */
    private $cadHauteur;

    /**
     * @var float
     *
     * @ORM\Column(name="cad_largeur", type="float", precision=10, scale=0, nullable=true)
     */
    private $cadLargeur;

    /**
     * @var float
     *
     * @ORM\Column(name="cad_longueur", type="float", precision=10, scale=0, nullable=true)
     */
    private $cadLongueur;

    /**
     * @var string
     *
     * @ORM\Column(name="cad_num_serie_type", type="string", length=100, nullable=true)
     */
    private $cadNumSerieType;

    /**
     * @var string
     *
     * @ORM\Column(name="cad_num_moteur", type="string", length=100, nullable=true)
     */
    private $cadNumMoteur;

    /**
     * @var string
     *
     * @ORM\Column(name="cad_premiere_circule", type="string", length=100, nullable=true)
     */
    private $cadPremiereCircule;

    /**
     * @var string
     *
     * @ORM\Column(name="cad_type_car", type="string", length=45, nullable=true)
     */
    private $cadTypeCar;

    /**
     * @var integer
     *
     * @ORM\Column(name="cad_nbr_assis", type="integer", nullable=true)
     */
    private $cadNbrAssis;

    /**
     * @var string
     *
     * @ORM\Column(name="cad_poids_maxima", type="text", length=65535, nullable=true)
     */
    private $cadPoidsMaxima;

    /**
     * @var \CtCarosserie
     *
     * @ORM\ManyToOne(targetEntity="CtCarosserie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_carosserie_id", referencedColumnName="id")
     * })
     */
    private $ctCarosserie;

    /**
     * @var \CtConstAvDedType
     *
     * @ORM\ManyToOne(targetEntity="CtConstAvDedType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_const_av_ded_type_id", referencedColumnName="id")
     * })
     */
    private $ctConstAvDedType;

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
     * @var \CtSourceEnergie
     *
     * @ORM\ManyToOne(targetEntity="CtSourceEnergie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_source_energie_id", referencedColumnName="id")
     * })
     */
    private $ctSourceEnergie;



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
     * Set cadPuissance
     *
     * @param float $cadPuissance
     *
     * @return CtConstAvDedCarac
     */
    public function setCadPuissance($cadPuissance)
    {
        $this->cadPuissance = $cadPuissance;

        return $this;
    }

    /**
     * Get cadPuissance
     *
     * @return float
     */
    public function getCadPuissance()
    {
        return $this->cadPuissance;
    }

    /**
     * Set cadPoidsVide
     *
     * @param float $cadPoidsVide
     *
     * @return CtConstAvDedCarac
     */
    public function setCadPoidsVide($cadPoidsVide)
    {
        $this->cadPoidsVide = $cadPoidsVide;

        return $this;
    }

    /**
     * Get cadPoidsVide
     *
     * @return float
     */
    public function getCadPoidsVide()
    {
        return $this->cadPoidsVide;
    }

    /**
     * Set cadChargeUtile
     *
     * @param float $cadChargeUtile
     *
     * @return CtConstAvDedCarac
     */
    public function setCadChargeUtile($cadChargeUtile)
    {
        $this->cadChargeUtile = $cadChargeUtile;

        return $this;
    }

    /**
     * Get cadChargeUtile
     *
     * @return float
     */
    public function getCadChargeUtile()
    {
        return $this->cadChargeUtile;
    }

    /**
     * Set cadHauteur
     *
     * @param float $cadHauteur
     *
     * @return CtConstAvDedCarac
     */
    public function setCadHauteur($cadHauteur)
    {
        $this->cadHauteur = $cadHauteur;

        return $this;
    }

    /**
     * Get cadHauteur
     *
     * @return float
     */
    public function getCadHauteur()
    {
        return $this->cadHauteur;
    }

    /**
     * Set cadLargeur
     *
     * @param float $cadLargeur
     *
     * @return CtConstAvDedCarac
     */
    public function setCadLargeur($cadLargeur)
    {
        $this->cadLargeur = $cadLargeur;

        return $this;
    }

    /**
     * Get cadLargeur
     *
     * @return float
     */
    public function getCadLargeur()
    {
        return $this->cadLargeur;
    }

    /**
     * Set cadLongueur
     *
     * @param float $cadLongueur
     *
     * @return CtConstAvDedCarac
     */
    public function setCadLongueur($cadLongueur)
    {
        $this->cadLongueur = $cadLongueur;

        return $this;
    }

    /**
     * Get cadLongueur
     *
     * @return float
     */
    public function getCadLongueur()
    {
        return $this->cadLongueur;
    }

    /**
     * Set cadNumSerieType
     *
     * @param string $cadNumSerieType
     *
     * @return CtConstAvDedCarac
     */
    public function setCadNumSerieType($cadNumSerieType)
    {
        $this->cadNumSerieType = $cadNumSerieType;

        return $this;
    }

    /**
     * Get cadNumSerieType
     *
     * @return string
     */
    public function getCadNumSerieType()
    {
        return $this->cadNumSerieType;
    }

    /**
     * Set cadNumMoteur
     *
     * @param string $cadNumMoteur
     *
     * @return CtConstAvDedCarac
     */
    public function setCadNumMoteur($cadNumMoteur)
    {
        $this->cadNumMoteur = $cadNumMoteur;

        return $this;
    }

    /**
     * Get cadNumMoteur
     *
     * @return string
     */
    public function getCadNumMoteur()
    {
        return $this->cadNumMoteur;
    }

    /**
     * Set cadTypeCar
     *
     * @param string $cadTypeCar
     *
     * @return CtConstAvDedCarac
     */
    public function setCadTypeCar($cadTypeCar)
    {
        $this->cadTypeCar = $cadTypeCar;

        return $this;
    }

    /**
     * Get cadTypeCar
     *
     * @return string
     */
    public function getCadTypeCar()
    {
        return $this->cadTypeCar;
    }

    /**
     * Set cadPoidsMaxima
     *
     * @param string $cadPoidsMaxima
     *
     * @return CtConstAvDedCarac
     */
    public function setCadPoidsMaxima($cadPoidsMaxima)
    {
        $this->cadPoidsMaxima = $cadPoidsMaxima;

        return $this;
    }

    /**
     * Get cadPoidsMaxima
     *
     * @return string
     */
    public function getCadPoidsMaxima()
    {
        return $this->cadPoidsMaxima;
    }

    /**
     * Set ctCarosserie
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtCarosserie $ctCarosserie
     *
     * @return CtConstAvDedCarac
     */
    public function setCtCarosserie(\Ct\Service\MetierManagerBundle\Entity\CtCarosserie $ctCarosserie = null)
    {
        $this->ctCarosserie = $ctCarosserie;

        return $this;
    }

    /**
     * Get ctCarosserie
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtCarosserie
     */
    public function getCtCarosserie()
    {
        return $this->ctCarosserie;
    }

    /**
     * Set ctConstAvDedType
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtConstAvDedType $ctConstAvDedType
     *
     * @return CtConstAvDedCarac
     */
    public function setCtConstAvDedType(\Ct\Service\MetierManagerBundle\Entity\CtConstAvDedType $ctConstAvDedType = null)
    {
        $this->ctConstAvDedType = $ctConstAvDedType;

        return $this;
    }

    /**
     * Get ctConstAvDedType
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtConstAvDedType
     */
    public function getCtConstAvDedType()
    {
        return $this->ctConstAvDedType;
    }

    /**
     * Set ctGenre
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtGenre $ctGenre
     *
     * @return CtConstAvDedCarac
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
     * @return CtConstAvDedCarac
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
     * Set ctSourceEnergie
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtSourceEnergie $ctSourceEnergie
     *
     * @return CtConstAvDedCarac
     */
    public function setCtSourceEnergie(\Ct\Service\MetierManagerBundle\Entity\CtSourceEnergie $ctSourceEnergie = null)
    {
        $this->ctSourceEnergie = $ctSourceEnergie;

        return $this;
    }

    /**
     * Get ctSourceEnergie
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtSourceEnergie
     */
    public function getCtSourceEnergie()
    {
        return $this->ctSourceEnergie;
    }

    /**
     * Set cadPoidsTotalCharge
     *
     * @param float $cadPoidsTotalCharge
     *
     * @return CtConstAvDedCarac
     */
    public function setCadPoidsTotalCharge($cadPoidsTotalCharge)
    {
        $this->cadPoidsTotalCharge = $cadPoidsTotalCharge;

        return $this;
    }

    /**
     * Get cadPoidsTotalCharge
     *
     * @return float
     */
    public function getCadPoidsTotalCharge()
    {
        return $this->cadPoidsTotalCharge;
    }

    /**
     * Set cadPremiereCircule
     *
     * @param string $cadPremiereCircule
     *
     * @return CtConstAvDedCarac
     */
    public function setCadPremiereCircule($cadPremiereCircule)
    {
        $this->cadPremiereCircule = $cadPremiereCircule;

        return $this;
    }

    /**
     * Get cadPremiereCircule
     *
     * @return string
     */
    public function getCadPremiereCircule()
    {
        return $this->cadPremiereCircule;
    }

    /**
     * Set cadCylindre
     *
     * @param string $cadCylindre
     *
     * @return CtConstAvDedCarac
     */
    public function setCadCylindre($cadCylindre)
    {
        $this->cadCylindre = $cadCylindre;

        return $this;
    }

    /**
     * Get cadCylindre
     *
     * @return string
     */
    public function getCadCylindre()
    {
        return $this->cadCylindre;
    }

    /**
     * Set cadNbrAssis
     *
     * @param integer $cadNbrAssis
     *
     * @return CtConstAvDedCarac
     */
    public function setCadNbrAssis($cadNbrAssis)
    {
        $this->cadNbrAssis = $cadNbrAssis;

        return $this;
    }

    /**
     * Get cadNbrAssis
     *
     * @return integer
     */
    public function getCadNbrAssis()
    {
        return $this->cadNbrAssis;
    }
}
