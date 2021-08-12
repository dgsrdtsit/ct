<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtConstAvDed
 *
 * @ORM\Table(name="ct_const_av_ded", indexes={
 *     @ORM\Index(name="fk_ct_const_av_ded_ct_user1_idx", columns={"ct_verificateur_id"}),
 *     @ORM\Index(name="fk_ct_const_av_ded_ct_centre1_idx", columns={"ct_centre_id"})
 * })
 * @ORM\Entity
 */
class CtConstAvDed
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
     * @ORM\Column(name="cad_immatriculation", type="string", length=45, nullable=true)
     */
    private $cadImmatriculation;

    /**
     * @var string
     *
     * @ORM\Column(name="cad_provenance", type="string", length=45, nullable=true)
     */
    private $cadProvenance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cad_date_embarquement", type="datetime", nullable=true)
     */
    private $cadDateEmbarquement;

    /**
     * @var string
     *
     * @ORM\Column(name="cad_lieu_embarquement", type="string", length=45, nullable=true)
     */
    private $cadLieuEmbarquement;

    /**
     * @var string
     *
     * @ORM\Column(name="cad_divers", type="string", length=100, nullable=true)
     */
    private $cadDivers;

    /**
     * @var string
     *
     * @ORM\Column(name="cad_proprietaire_nom", type="string", length=100, nullable=true)
     */
    private $cadProprietaireNom;

    /**
     * @var string
     *
     * @ORM\Column(name="cad_proprietaire_adresse", type="string", length=100, nullable=true)
     */
    private $cadProprietaireAdresse;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cad_bon_etat", type="boolean", nullable=true)
     */
    private $cadBonEtat;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cad_conforme", type="boolean", nullable=true)
     */
    private $cadConforme;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cad_sec_pers", type="boolean", nullable=true)
     */
    private $cadSecPers;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cad_sec_march", type="boolean", nullable=true)
     */
    private $cadSecMarch;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cad_protec_env", type="boolean", nullable=true)
     */
    private $cadProtecEnv;

    /**
     * @var string
     *
     * @ORM\Column(name="cad_numero", type="string", length=45, nullable=true)
     */
    private $cadNumero;

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
     * @var \CtUser
     *
     * @ORM\ManyToOne(targetEntity="Ct\Service\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_verificateur_id", referencedColumnName="id")
     * })
     */
    private $ctVerificateur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cad_created", type="datetime", nullable=true)
     */
    private $cadCreated;


    /**
     * Many CtConstAvDed have Many CtConstAvDedCarac.
     * @ORM\ManyToMany(targetEntity="Ct\Service\MetierManagerBundle\Entity\CtConstAvDedCarac", cascade={"persist"})
     * @ORM\JoinTable(name="ct_const_av_deds_const_av_ded_caracs",
     *      joinColumns={@ORM\JoinColumn(name="const_av_ded_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="const_av_ded_carac_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $ctConstAvDedCaracs;

    /**
     * @var string
     *
     * @ORM\Column(name="cad_observation", type="string", length=255, nullable=true)
     */
    private $cadObservation;

    public function __construct()
    {
        $this->ctConstAvDedCaracs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cadCreated = new \DateTime();
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
     * Set cadProvenance
     *
     * @param string $cadProvenance
     *
     * @return CtConstAvDed
     */
    public function setCadProvenance($cadProvenance)
    {
        $this->cadProvenance = $cadProvenance;

        return $this;
    }

    /**
     * Get cadProvenance
     *
     * @return string
     */
    public function getCadProvenance()
    {
        return $this->cadProvenance;
    }

    /**
     * Set cadDivers
     *
     * @param string $cadDivers
     *
     * @return CtConstAvDed
     */
    public function setCadDivers($cadDivers)
    {
        $this->cadDivers = $cadDivers;

        return $this;
    }

    /**
     * Get cadDivers
     *
     * @return string
     */
    public function getCadDivers()
    {
        return $this->cadDivers;
    }

    /**
     * Set cadProprietaireNom
     *
     * @param string $cadProprietaireNom
     *
     * @return CtConstAvDed
     */
    public function setCadProprietaireNom($cadProprietaireNom)
    {
        $this->cadProprietaireNom = $cadProprietaireNom;

        return $this;
    }

    /**
     * Get cadProprietaireNom
     *
     * @return string
     */
    public function getCadProprietaireNom()
    {
        return $this->cadProprietaireNom;
    }

    /**
     * Set cadProprietaireAdresse
     *
     * @param string $cadProprietaireAdresse
     *
     * @return CtConstAvDed
     */
    public function setCadProprietaireAdresse($cadProprietaireAdresse)
    {
        $this->cadProprietaireAdresse = $cadProprietaireAdresse;

        return $this;
    }

    /**
     * Get cadProprietaireAdresse
     *
     * @return string
     */
    public function getCadProprietaireAdresse()
    {
        return $this->cadProprietaireAdresse;
    }

    /**
     * Set cadBonEtat
     *
     * @param boolean $cadBonEtat
     *
     * @return CtConstAvDed
     */
    public function setCadBonEtat($cadBonEtat)
    {
        $this->cadBonEtat = $cadBonEtat;

        return $this;
    }

    /**
     * Get cadBonEtat
     *
     * @return boolean
     */
    public function getCadBonEtat()
    {
        return $this->cadBonEtat;
    }

    /**
     * Set cadSecPers
     *
     * @param boolean $cadSecPers
     *
     * @return CtConstAvDed
     */
    public function setCadSecPers($cadSecPers)
    {
        $this->cadSecPers = $cadSecPers;

        return $this;
    }

    /**
     * Get cadSecPers
     *
     * @return boolean
     */
    public function getCadSecPers()
    {
        return $this->cadSecPers;
    }

    /**
     * Set cadSecMarch
     *
     * @param boolean $cadSecMarch
     *
     * @return CtConstAvDed
     */
    public function setCadSecMarch($cadSecMarch)
    {
        $this->cadSecMarch = $cadSecMarch;

        return $this;
    }

    /**
     * Get cadSecMarch
     *
     * @return boolean
     */
    public function getCadSecMarch()
    {
        return $this->cadSecMarch;
    }

    /**
     * Set cadNumero
     *
     * @param string $cadNumero
     *
     * @return CtConstAvDed
     */
    public function setCadNumero($cadNumero)
    {
        $this->cadNumero = $cadNumero;

        return $this;
    }

    /**
     * Get cadNumero
     *
     * @return string
     */
    public function getCadNumero()
    {
        return $this->cadNumero;
    }

    /**
     * Set ctCentre
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtCentre $ctCentre
     *
     * @return CtConstAvDed
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
     * Set ctVerificateur
     *
     * @param \Ct\Service\UserBundle\Entity\User $ctVerificateur
     *
     * @return CtConstAvDed
     */
    public function setCtVerificateur(\Ct\Service\UserBundle\Entity\User $ctVerificateur = null)
    {
        $this->ctVerificateur = $ctVerificateur;

        return $this;
    }

    /**
     * Get ctVerificateur
     *
     * @return \Ct\Service\UserBundle\Entity\User
     */
    public function getCtVerificateur()
    {
        return $this->ctVerificateur;
    }

    /**
     * Set cadImmatriculation
     *
     * @param string $cadImmatriculation
     *
     * @return CtConstAvDed
     */
    public function setCadImmatriculation($cadImmatriculation)
    {
        $this->cadImmatriculation = $cadImmatriculation;

        return $this;
    }

    /**
     * Get cadImmatriculation
     *
     * @return string
     */
    public function getCadImmatriculation()
    {
        return $this->cadImmatriculation;
    }

    /**
     * Set cadDateEmbarquement
     *
     * @param \DateTime $cadDateEmbarquement
     *
     * @return CtConstAvDed
     */
    public function setCadDateEmbarquement($cadDateEmbarquement)
    {
        $this->cadDateEmbarquement = $cadDateEmbarquement;

        return $this;
    }

    /**
     * Get cadDateEmbarquement
     *
     * @return \DateTime
     */
    public function getCadDateEmbarquement()
    {
        return $this->cadDateEmbarquement;
    }

    /**
     * Set cadLieuEmbarquement
     *
     * @param string $cadLieuEmbarquement
     *
     * @return CtConstAvDed
     */
    public function setCadLieuEmbarquement($cadLieuEmbarquement)
    {
        $this->cadLieuEmbarquement = $cadLieuEmbarquement;

        return $this;
    }

    /**
     * Get cadLieuEmbarquement
     *
     * @return string
     */
    public function getCadLieuEmbarquement()
    {
        return $this->cadLieuEmbarquement;
    }

    /**
     * Set cadCreated
     *
     * @param \DateTime $cadCreated
     *
     * @return CtConstAvDed
     */
    public function setCadCreated($cadCreated)
    {
        $this->cadCreated = $cadCreated;

        return $this;
    }

    /**
     * Get cadCreated
     *
     * @return \DateTime
     */
    public function getCadCreated()
    {
        return $this->cadCreated;
    }

    /**
     * Add ctConstAvDedCarac
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtConstAvDedCarac $ctConstAvDedCarac
     *
     * @return CtConstAvDed
     */
    public function addCtConstAvDedCarac(\Ct\Service\MetierManagerBundle\Entity\CtConstAvDedCarac $ctConstAvDedCarac)
    {
        $this->ctConstAvDedCaracs[] = $ctConstAvDedCarac;

        return $this;
    }

    /**
     * Remove ctConstAvDedCarac
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtConstAvDedCarac $ctConstAvDedCarac
     */
    public function removeCtConstAvDedCarac(\Ct\Service\MetierManagerBundle\Entity\CtConstAvDedCarac $ctConstAvDedCarac)
    {
        $this->ctConstAvDedCaracs->removeElement($ctConstAvDedCarac);
    }

    /**
     * Get ctConstAvDedCaracs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCtConstAvDedCaracs()
    {
        return $this->ctConstAvDedCaracs;
    }

    /**
     * Set cadConforme
     *
     * @param boolean $cadConforme
     *
     * @return CtConstAvDed
     */
    public function setCadConforme($cadConforme)
    {
        $this->cadConforme = $cadConforme;

        return $this;
    }

    /**
     * Get cadConforme
     *
     * @return boolean
     */
    public function getCadConforme()
    {
        return $this->cadConforme;
    }

    /**
     * Set cadProtecEnv
     *
     * @param boolean $cadProtecEnv
     *
     * @return CtConstAvDed
     */
    public function setCadProtecEnv($cadProtecEnv)
    {
        $this->cadProtecEnv = $cadProtecEnv;

        return $this;
    }

    /**
     * Get cadProtecEnv
     *
     * @return boolean
     */
    public function getCadProtecEnv()
    {
        return $this->cadProtecEnv;
    }

    /**
     * Set cadObservation
     *
     * @param string $cadObservation
     *
     * @return CtConstAvDed
     */
    public function setCadObservation($cadObservation)
    {
        $this->cadObservation = $cadObservation;

        return $this;
    }

    /**
     * Get cadObservation
     *
     * @return string
     */
    public function getCadObservation()
    {
        return $this->cadObservation;
    }
}
