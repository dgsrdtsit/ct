<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtReception
 *
 * @ORM\Table(name="ct_reception", indexes={@ORM\Index(name="fk_ct_reception_ct_type_reception1_idx", columns={"ct_type_reception_id"}), @ORM\Index(name="fk_ct_reception_ct_motif1_idx", columns={"ct_motif_id"}), @ORM\Index(name="fk_ct_reception_ct_utilisation1_idx", columns={"ct_utilisation_id"}), @ORM\Index(name="fk_ct_reception_ct_user1_idx", columns={"ct_user_id"}), @ORM\Index(name="fk_ct_reception_ct_vehicule1_idx", columns={"ct_vehicule_id"}), @ORM\Index(name="fk_ct_reception_ct_centre1_idx", columns={"ct_centre_id"})})
 * @ORM\Entity
 */
class CtReception
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
     * @ORM\Column(name="rcp_mise_service", type="date", nullable=true)
     */
    private $rcpMiseService;

    /**
     * @var string
     *
     * @ORM\Column(name="rcp_immatriculation", type="string", length=45, nullable=true)
     */
    private $rcpImmatriculation;

    /**
     * @var string
     *
     * @ORM\Column(name="rcp_proprietaire", type="string", length=255, nullable=true)
     */
    private $rcpProprietaire;

    /**
     * @var string
     *
     * @ORM\Column(name="rcp_profession", type="string", length=100, nullable=true)
     */
    private $rcpProfession;

    /**
     * @var string
     *
     * @ORM\Column(name="rcp_adresse", type="string", length=255, nullable=true)
     */
    private $rcpAdresse;

    /**
     * @var integer
     *
     * @ORM\Column(name="rcp_nbr_assis", type="integer", nullable=true)
     */
    private $rcpNbrAssis;

    /**
     * @var integer
     *
     * @ORM\Column(name="rcp_nbr_debout", type="integer", nullable=true)
     */
    private $rcpNbrDebout;

    /**
     * @var string
     *
     * @ORM\Column(name="rcp_num_pv", type="string", length=100, nullable=true)
     */
    private $rcpNumPv;

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
     * @var \CtMotif
     *
     * @ORM\ManyToOne(targetEntity="CtMotif")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_motif_id", referencedColumnName="id")
     * })
     */
    private $ctMotif;

    /**
     * @var \CtTypeReception
     *
     * @ORM\ManyToOne(targetEntity="CtTypeReception")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_type_reception_id", referencedColumnName="id")
     * })
     */
    private $ctTypeReception;

    /**
     * @var \CtUser
     *
     * @ORM\ManyToOne(targetEntity="Ct\Service\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_user_id", referencedColumnName="id")
     * })
     */
    private $ctUser;

    /**
     * @var \CtUtilisation
     *
     * @ORM\ManyToOne(targetEntity="CtUtilisation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_utilisation_id", referencedColumnName="id")
     * })
     */
    private $ctUtilisation;

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
     * @var \CtSourceEnergie
     *
     * @ORM\ManyToOne(targetEntity="CtSourceEnergie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_source_energie_id", referencedColumnName="id")
     * })
     */
    private $ctSourceEnergie;

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
     * @var string
     *
     * @ORM\Column(name="rcp_num_group", type="string", nullable=true)
     */
    private $rcpNumGroup;

    /**
     * @var CtGenre
     *
     * @ORM\ManyToOne(targetEntity="CtGenre")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_genre_id", referencedColumnName="id")
     * })
     */
    private $ctGenre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="rcp_created", type="date", nullable=true)
     */
    private $rcpCreated;

    public function __construct()
    {
        $this->rcpCreated = new \DateTime();
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
     * Set rcpMiseService
     *
     * @param \DateTime $rcpMiseService
     *
     * @return CtReception
     */
    public function setRcpMiseService($rcpMiseService)
    {
        $this->rcpMiseService = $rcpMiseService;

        return $this;
    }

    /**
     * Get rcpMiseService
     *
     * @return \DateTime
     */
    public function getRcpMiseService()
    {
        return $this->rcpMiseService;
    }

    /**
     * Set rcpImmatriculation
     *
     * @param string $rcpImmatriculation
     *
     * @return CtReception
     */
    public function setRcpImmatriculation($rcpImmatriculation)
    {
        $this->rcpImmatriculation = $rcpImmatriculation;

        return $this;
    }

    /**
     * Get rcpImmatriculation
     *
     * @return string
     */
    public function getRcpImmatriculation()
    {
        return $this->rcpImmatriculation;
    }

    /**
     * Set rcpProprietaire
     *
     * @param string $rcpProprietaire
     *
     * @return CtReception
     */
    public function setRcpProprietaire($rcpProprietaire)
    {
        $this->rcpProprietaire = $rcpProprietaire;

        return $this;
    }

    /**
     * Get rcpProprietaire
     *
     * @return string
     */
    public function getRcpProprietaire()
    {
        return $this->rcpProprietaire;
    }

    /**
     * Set rcpProfession
     *
     * @param string $rcpProfession
     *
     * @return CtReception
     */
    public function setRcpProfession($rcpProfession)
    {
        $this->rcpProfession = $rcpProfession;

        return $this;
    }

    /**
     * Get rcpProfession
     *
     * @return string
     */
    public function getRcpProfession()
    {
        return $this->rcpProfession;
    }

    /**
     * Set rcpAdresse
     *
     * @param string $rcpAdresse
     *
     * @return CtReception
     */
    public function setRcpAdresse($rcpAdresse)
    {
        $this->rcpAdresse = $rcpAdresse;

        return $this;
    }

    /**
     * Get rcpAdresse
     *
     * @return string
     */
    public function getRcpAdresse()
    {
        return $this->rcpAdresse;
    }

    /**
     * Set rcpNbrAssis
     *
     * @param integer $rcpNbrAssis
     *
     * @return CtReception
     */
    public function setRcpNbrAssis($rcpNbrAssis)
    {
        $this->rcpNbrAssis = $rcpNbrAssis;

        return $this;
    }

    /**
     * Get rcpNbrAssis
     *
     * @return integer
     */
    public function getRcpNbrAssis()
    {
        return $this->rcpNbrAssis;
    }

    /**
     * Set rcpNbrDebout
     *
     * @param integer $rcpNbrDebout
     *
     * @return CtReception
     */
    public function setRcpNbrDebout($rcpNbrDebout)
    {
        $this->rcpNbrDebout = $rcpNbrDebout;

        return $this;
    }

    /**
     * Get rcpNbrDebout
     *
     * @return integer
     */
    public function getRcpNbrDebout()
    {
        return $this->rcpNbrDebout;
    }

    /**
     * Set rcpNumPv
     *
     * @param string $rcpNumPv
     *
     * @return CtReception
     */
    public function setRcpNumPv($rcpNumPv)
    {
        $this->rcpNumPv = $rcpNumPv;

        return $this;
    }

    /**
     * Get rcpNumPv
     *
     * @return string
     */
    public function getRcpNumPv()
    {
        return $this->rcpNumPv;
    }

    /**
     * Set ctCentre
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtCentre $ctCentre
     *
     * @return CtReception
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
     * Set ctMotif
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtMotif $ctMotif
     *
     * @return CtReception
     */
    public function setCtMotif(\Ct\Service\MetierManagerBundle\Entity\CtMotif $ctMotif = null)
    {
        $this->ctMotif = $ctMotif;

        return $this;
    }

    /**
     * Get ctMotif
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtMotif
     */
    public function getCtMotif()
    {
        return $this->ctMotif;
    }

    /**
     * Set ctTypeReception
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtTypeReception $ctTypeReception
     *
     * @return CtReception
     */
    public function setCtTypeReception(\Ct\Service\MetierManagerBundle\Entity\CtTypeReception $ctTypeReception = null)
    {
        $this->ctTypeReception = $ctTypeReception;

        return $this;
    }

    /**
     * Get ctTypeReception
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtTypeReception
     */
    public function getCtTypeReception()
    {
        return $this->ctTypeReception;
    }

    /**
     * Set ctUser
     *
     * @param \Ct\Service\UserBundle\Entity\User $ctUser
     *
     * @return CtReception
     */
    public function setCtUser(\Ct\Service\UserBundle\Entity\User $ctUser = null)
    {
        $this->ctUser = $ctUser;

        return $this;
    }

    /**
     * Get ctUser
     *
     * @return \Ct\Service\UserBundle\Entity\User
     */
    public function getCtUser()
    {
        return $this->ctUser;
    }

    /**
     * Set ctUtilisation
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtUtilisation $ctUtilisation
     *
     * @return CtReception
     */
    public function setCtUtilisation(\Ct\Service\MetierManagerBundle\Entity\CtUtilisation $ctUtilisation = null)
    {
        $this->ctUtilisation = $ctUtilisation;

        return $this;
    }

    /**
     * Get ctUtilisation
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtUtilisation
     */
    public function getCtUtilisation()
    {
        return $this->ctUtilisation;
    }

    /**
     * Set ctVehicule
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtVehicule $ctVehicule
     *
     * @return CtReception
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
     * Set ctSourceEnergie
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtSourceEnergie $ctSourceEnergie
     *
     * @return CtReception
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
     * Set ctCarosserie
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtCarosserie $ctCarosserie
     *
     * @return CtReception
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
     * Set rcpNumGroup
     *
     * @param string $rcpNumGroup
     *
     * @return CtReception
     */
    public function setRcpNumGroup($rcpNumGroup)
    {
        $this->rcpNumGroup = $rcpNumGroup;

        return $this;
    }

    /**
     * Get rcpNumGroup
     *
     * @return string
     */
    public function getRcpNumGroup()
    {
        return $this->rcpNumGroup;
    }

    /**
     * Set rcpCreated
     *
     * @param \DateTime $rcpCreated
     *
     * @return CtReception
     */
    public function setRcpCreated($rcpCreated)
    {
        $this->rcpCreated = $rcpCreated;

        return $this;
    }

    /**
     * Get rcpCreated
     *
     * @return \DateTime
     */
    public function getRcpCreated()
    {
        return $this->rcpCreated;
    }

    /**
     * Set ctGenre
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtGenre $ctGenre
     *
     * @return CtReception
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
}
