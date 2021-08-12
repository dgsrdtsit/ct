<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtCarteGrise
 *
 * @ORM\Table(name="ct_carte_grise", indexes={@ORM\Index(name="fk_ct_carte_grise_ct_carosserie1_idx", columns={"ct_carosserie_id"}), @ORM\Index(name="fk_ct_carte_grise_ct_source_energie1_idx", columns={"ct_source_energie_id"}), @ORM\Index(name="fk_ct_carte_grise_ct_vehicule1_idx", columns={"ct_vehicule_id"}), @ORM\Index(name="fk_ct_carte_grise_ct_centre1_idx", columns={"ct_centre_id"})})
 * @ORM\Entity
 */
class CtCarteGrise
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
     * @var \DateTime
     *
     * @ORM\Column(name="cg_date_emission", type="date", nullable=true)
     */
    private $cgDateEmission;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_immatriculation", unique=true, type="string", length=45, nullable=true)
     */
    private $cgImmatriculation;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_num_identification", unique=true, type="string", length=45, nullable=true)
     */
    private $cgNumIdentification;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_nom", type="string", length=255, nullable=true)
     */
    private $cgNom;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_prenom", type="string", length=255, nullable=true)
     */
    private $cgPrenom;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_profession", type="string", length=255, nullable=true)
     */
    private $cgProfession;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_adresse", type="string", length=255, nullable=true)
     */
    private $cgAdresse;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_phone", type="string", length=255, nullable=true)
     */
    private $cgPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_commune", type="string", length=255, nullable=true)
     */
    private $cgCommune;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_zone_deserte", type="string", length=255, nullable=true)
     */
    private $cgZoneDeserte;

    /**
     * @var integer
     *
     * @ORM\Column(name="cg_nbr_assis", type="integer", nullable=true)
     */
    private $cgNbrAssis;

    /**
     * @var integer
     *
     * @ORM\Column(name="cg_nbr_debout", type="integer", nullable=true)
     */
    private $cgNbrDebout;

    /**
     * @var integer
     *
     * @ORM\Column(name="cg_puissance_admin", type="integer", nullable=true)
     */
    private $cgPuissanceAdmin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cg_mise_en_service", type="date", nullable=true)
     */
    private $cgMiseEnService;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_patente", type="string", length=255, nullable=true)
     */
    private $cgPatente;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_ani", type="string", length=255, nullable=true)
     */
    private $cgAni;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_rta", type="string", length=255, nullable=true)
     */
    private $cgRta;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_num_carte_violette", type="string", length=255, nullable=true)
     */
    private $cgNumCarteViolette;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cg_date_carte_violette", type="date", nullable=true)
     */
    private $cgDateCarteViolette;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_lieu_carte_violette", type="string", length=255, nullable=true)
     */
    private $cgLieuCarteViolette;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_num_vignette", type="string", length=255, nullable=true)
     */
    private $cgNumVignette;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cg_date_vignette", type="date", nullable=true)
     */
    private $cgDateVignette;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_lieu_vignette", type="string", length=255, nullable=true)
     */
    private $cgLieuVignette;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cg_created", type="datetime", nullable=true)
     */
    private $cgCreated;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_nom_cooperative", type="string", length=100, nullable=true)
     */
    private $cgNomCooperative;

    /**
     * @var string
     *
     * @ORM\Column(name="cg_itineraire", type="string", length=100, nullable=true)
     */
    private $cgItineraire;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cg_is_transport", type="boolean", nullable=false)
     */
    private $cgIsTransport = false;

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
     * @var \CtCentre
     *
     * @ORM\ManyToOne(targetEntity="CtCentre")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_centre_id", referencedColumnName="id")
     * })
     */
    private $ctCentre;

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
     * @var \CtVehicule
     *
     * @ORM\ManyToOne(targetEntity="CtVehicule", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_vehicule_id", referencedColumnName="id")
     * })
     */
    private $ctVehicule;

    /**
     * @var CtZoneDeserte
     *
     * @ORM\ManyToOne(targetEntity="Ct\Service\MetierManagerBundle\Entity\CtZoneDeserte", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_zone_deserte_id", referencedColumnName="id")
     * })
     */
    private $ctZoneDeserte;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cgCreated = new \DateTime();
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
     * Set cgDateEmission
     *
     * @param \DateTime $cgDateEmission
     *
     * @return CtCarteGrise
     */
    public function setCgDateEmission($cgDateEmission)
    {
        $this->cgDateEmission = $cgDateEmission;

        return $this;
    }

    /**
     * Get cgDateEmission
     *
     * @return \DateTime
     */
    public function getCgDateEmission()
    {
        return $this->cgDateEmission;
    }

    /**
     * Set cgNom
     *
     * @param string $cgNom
     *
     * @return CtCarteGrise
     */
    public function setCgNom($cgNom)
    {
        $this->cgNom = $cgNom;

        return $this;
    }

    /**
     * Get cgNom
     *
     * @return string
     */
    public function getCgNom()
    {
        return $this->cgNom;
    }

    /**
     * Set cgPrenom
     *
     * @param string $cgPrenom
     *
     * @return CtCarteGrise
     */
    public function setCgPrenom($cgPrenom)
    {
        $this->cgPrenom = $cgPrenom;

        return $this;
    }

    /**
     * Get cgPrenom
     *
     * @return string
     */
    public function getCgPrenom()
    {
        return $this->cgPrenom;
    }

    /**
     * Set cgProfession
     *
     * @param string $cgProfession
     *
     * @return CtCarteGrise
     */
    public function setCgProfession($cgProfession)
    {
        $this->cgProfession = $cgProfession;

        return $this;
    }

    /**
     * Get cgProfession
     *
     * @return string
     */
    public function getCgProfession()
    {
        return $this->cgProfession;
    }

    /**
     * Set cgAdresse
     *
     * @param string $cgAdresse
     *
     * @return CtCarteGrise
     */
    public function setCgAdresse($cgAdresse)
    {
        $this->cgAdresse = $cgAdresse;

        return $this;
    }

    /**
     * Get cgAdresse
     *
     * @return string
     */
    public function getCgAdresse()
    {
        return $this->cgAdresse;
    }

    /**
     * Set cgPhone
     *
     * @param string $cgPhone
     *
     * @return CtCarteGrise
     */
    public function setCgPhone($cgPhone)
    {
        $this->cgPhone = $cgPhone;

        return $this;
    }

    /**
     * Get cgPhone
     *
     * @return string
     */
    public function getCgPhone()
    {
        return $this->cgPhone;
    }

    /**
     * Set cgCommune
     *
     * @param string $cgCommune
     *
     * @return CtCarteGrise
     */
    public function setCgCommune($cgCommune)
    {
        $this->cgCommune = $cgCommune;

        return $this;
    }

    /**
     * Get cgCommune
     *
     * @return string
     */
    public function getCgCommune()
    {
        return $this->cgCommune;
    }

    /**
     * Set cgNbrAssis
     *
     * @param integer $cgNbrAssis
     *
     * @return CtCarteGrise
     */
    public function setCgNbrAssis($cgNbrAssis)
    {
        $this->cgNbrAssis = $cgNbrAssis;

        return $this;
    }

    /**
     * Get cgNbrAssis
     *
     * @return integer
     */
    public function getCgNbrAssis()
    {
        return $this->cgNbrAssis;
    }

    /**
     * Set cgNbrDebout
     *
     * @param integer $cgNbrDebout
     *
     * @return CtCarteGrise
     */
    public function setCgNbrDebout($cgNbrDebout)
    {
        $this->cgNbrDebout = $cgNbrDebout;

        return $this;
    }

    /**
     * Get cgNbrDebout
     *
     * @return integer
     */
    public function getCgNbrDebout()
    {
        return $this->cgNbrDebout;
    }

    /**
     * Set cgPuissanceAdmin
     *
     * @param integer $cgPuissanceAdmin
     *
     * @return CtCarteGrise
     */
    public function setCgPuissanceAdmin($cgPuissanceAdmin)
    {
        $this->cgPuissanceAdmin = $cgPuissanceAdmin;

        return $this;
    }

    /**
     * Get cgPuissanceAdmin
     *
     * @return integer
     */
    public function getCgPuissanceAdmin()
    {
        return $this->cgPuissanceAdmin;
    }

    /**
     * Set cgMiseEnService
     *
     * @param \DateTime $cgMiseEnService
     *
     * @return CtCarteGrise
     */
    public function setCgMiseEnService($cgMiseEnService)
    {
        $this->cgMiseEnService = $cgMiseEnService;

        return $this;
    }

    /**
     * Get cgMiseEnService
     *
     * @return \DateTime
     */
    public function getCgMiseEnService()
    {
        return $this->cgMiseEnService;
    }

    /**
     * Set cgPatente
     *
     * @param string $cgPatente
     *
     * @return CtCarteGrise
     */
    public function setCgPatente($cgPatente)
    {
        $this->cgPatente = $cgPatente;

        return $this;
    }

    /**
     * Get cgPatente
     *
     * @return string
     */
    public function getCgPatente()
    {
        return $this->cgPatente;
    }

    /**
     * Set cgAni
     *
     * @param string $cgAni
     *
     * @return CtCarteGrise
     */
    public function setCgAni($cgAni)
    {
        $this->cgAni = $cgAni;

        return $this;
    }

    /**
     * Get cgAni
     *
     * @return string
     */
    public function getCgAni()
    {
        return $this->cgAni;
    }

    /**
     * Set cgRta
     *
     * @param string $cgRta
     *
     * @return CtCarteGrise
     */
    public function setCgRta($cgRta)
    {
        $this->cgRta = $cgRta;

        return $this;
    }

    /**
     * Get cgRta
     *
     * @return string
     */
    public function getCgRta()
    {
        return $this->cgRta;
    }

    /**
     * Set cgNumCarteViolette
     *
     * @param string $cgNumCarteViolette
     *
     * @return CtCarteGrise
     */
    public function setCgNumCarteViolette($cgNumCarteViolette)
    {
        $this->cgNumCarteViolette = $cgNumCarteViolette;

        return $this;
    }

    /**
     * Get cgNumCarteViolette
     *
     * @return string
     */
    public function getCgNumCarteViolette()
    {
        return $this->cgNumCarteViolette;
    }

    /**
     * Set cgDateCarteViolette
     *
     * @param \DateTime $cgDateCarteViolette
     *
     * @return CtCarteGrise
     */
    public function setCgDateCarteViolette($cgDateCarteViolette)
    {
        $this->cgDateCarteViolette = $cgDateCarteViolette;

        return $this;
    }

    /**
     * Get cgDateCarteViolette
     *
     * @return \DateTime
     */
    public function getCgDateCarteViolette()
    {
        return $this->cgDateCarteViolette;
    }

    /**
     * Set cgLieuCarteViolette
     *
     * @param string $cgLieuCarteViolette
     *
     * @return CtCarteGrise
     */
    public function setCgLieuCarteViolette($cgLieuCarteViolette)
    {
        $this->cgLieuCarteViolette = $cgLieuCarteViolette;

        return $this;
    }

    /**
     * Get cgLieuCarteViolette
     *
     * @return string
     */
    public function getCgLieuCarteViolette()
    {
        return $this->cgLieuCarteViolette;
    }

    /**
     * Set cgNumVignette
     *
     * @param string $cgNumVignette
     *
     * @return CtCarteGrise
     */
    public function setCgNumVignette($cgNumVignette)
    {
        $this->cgNumVignette = $cgNumVignette;

        return $this;
    }

    /**
     * Get cgNumVignette
     *
     * @return string
     */
    public function getCgNumVignette()
    {
        return $this->cgNumVignette;
    }

    /**
     * Set cgDateVignette
     *
     * @param \DateTime $cgDateVignette
     *
     * @return CtCarteGrise
     */
    public function setCgDateVignette($cgDateVignette)
    {
        $this->cgDateVignette = $cgDateVignette;

        return $this;
    }

    /**
     * Get cgDateVignette
     *
     * @return \DateTime
     */
    public function getCgDateVignette()
    {
        return $this->cgDateVignette;
    }

    /**
     * Set cgLieuVignette
     *
     * @param string $cgLieuVignette
     *
     * @return CtCarteGrise
     */
    public function setCgLieuVignette($cgLieuVignette)
    {
        $this->cgLieuVignette = $cgLieuVignette;

        return $this;
    }

    /**
     * Get cgLieuVignette
     *
     * @return string
     */
    public function getCgLieuVignette()
    {
        return $this->cgLieuVignette;
    }

    /**
     * Set ctCarosserie
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtCarosserie $ctCarosserie
     *
     * @return CtCarteGrise
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
     * Set ctCentre
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtCentre $ctCentre
     *
     * @return CtCarteGrise
     */
    public function setCtCentre(\Ct\Service\MetierManagerBundle\Entity\CtCentre $ctCentre = null)
    {
        $this->ctCentre = $ctCentre;

        return $this;
    }

    /**
     * Get ctCentre
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtCentre
     */
    public function getCtCentre()
    {
        return $this->ctCentre;
    }

    /**
     * Set ctSourceEnergie
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtSourceEnergie $ctSourceEnergie
     *
     * @return CtCarteGrise
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
     * Set ctVehicule
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtVehicule $ctVehicule
     *
     * @return CtCarteGrise
     */
    public function setCtVehicule(\Ct\Service\MetierManagerBundle\Entity\CtVehicule $ctVehicule = null)
    {
        $this->ctVehicule = $ctVehicule;

        return $this;
    }

    /**
     * Get ctVehicule
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtVehicule
     */
    public function getCtVehicule()
    {
        return $this->ctVehicule;
    }

    /**
     * Set cgImmatriculation
     *
     * @param string $cgImmatriculation
     *
     * @return CtCarteGrise
     */
    public function setCgImmatriculation($cgImmatriculation)
    {
        $this->cgImmatriculation = $cgImmatriculation;

        return $this;
    }

    /**
     * Get cgImmatriculation
     *
     * @return string
     */
    public function getCgImmatriculation()
    {
        return $this->cgImmatriculation;
    }

    /**
     * Set cgCreated
     *
     * @param \DateTime $cgCreated
     *
     * @return CtCarteGrise
     */
    public function setCgCreated($cgCreated)
    {
        $this->cgCreated = $cgCreated;

        return $this;
    }

    /**
     * Get cgCreated
     *
     * @return \DateTime
     */
    public function getCgCreated()
    {
        return $this->cgCreated;
    }

    /**
     * Set cgNomCooperative
     *
     * @param string $cgNomCooperative
     *
     * @return CtCarteGrise
     */
    public function setCgNomCooperative($cgNomCooperative)
    {
        $this->cgNomCooperative = $cgNomCooperative;

        return $this;
    }

    /**
     * Get cgNomCooperative
     *
     * @return string
     */
    public function getCgNomCooperative()
    {
        return $this->cgNomCooperative;
    }

    /**
     * Set cgItineraire
     *
     * @param string $cgItineraire
     *
     * @return CtCarteGrise
     */
    public function setCgItineraire($cgItineraire)
    {
        $this->cgItineraire = $cgItineraire;

        return $this;
    }

    /**
     * Get cgItineraire
     *
     * @return string
     */
    public function getCgItineraire()
    {
        return $this->cgItineraire;
    }

    /**
     * Set cgIsTransport
     *
     * @param boolean $cgIsTransport
     *
     * @return CtCarteGrise
     */
    public function setCgIsTransport($cgIsTransport)
    {
        $this->cgIsTransport = $cgIsTransport;

        return $this;
    }

    /**
     * Get cgIsTransport
     *
     * @return boolean
     */
    public function getCgIsTransport()
    {
        return $this->cgIsTransport;
    }

    /**
     * @return string
     */
    public function getCgNumIdentification()
    {
        return $this->cgNumIdentification;
    }

    /**
     * @param string $cgNumIdentification
     */
    public function setCgNumIdentification($cgNumIdentification)
    {
        $this->cgNumIdentification = $cgNumIdentification;
    }

    /**
     * @return string
     */
    public function getCgZoneDeserte()
    {
        return $this->cgZoneDeserte;
    }

    /**
     * @param string $cgZoneDeserte
     */
    public function setCgZoneDeserte($cgZoneDeserte)
    {
        $this->cgZoneDeserte = $cgZoneDeserte;
    }

    /**
     * @return CtZoneDeserte
     */
    public function getCtZoneDeserte()
    {
        return $this->ctZoneDeserte;
    }

    /**
     * @param CtZoneDeserte $ctZoneDeserte
     */
    public function setCtZoneDeserte($ctZoneDeserte)
    {
        $this->ctZoneDeserte = $ctZoneDeserte;
    }
}
