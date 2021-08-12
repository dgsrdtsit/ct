<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtVisite
 *
 * @ORM\Table(name="ct_visite",
 *  indexes={
 *      @ORM\Index(name="fk_ct_visite_ct_carte_grise1_idx", columns={"ct_carte_grise_id"}),
 *      @ORM\Index(name="fk_ct_visite_ct_usage1_idx", columns={"ct_usage_id"}),
 *      @ORM\Index(name="fk_ct_visite_ct_user1_idx", columns={"ct_user_id"}),
 *      @ORM\Index(name="fk_ct_visite_ct_type_visite1_idx", columns={"ct_type_visite_id"}),
 *      @ORM\Index(name="fk_ct_visite_ct_user2_idx", columns={"ct_verificateur_id"}),
 *      @ORM\Index(name="fk_ct_visite_ct_centre1_idx", columns={"ct_centre_id"})
 * })
 * @ORM\Entity
 */
class CtVisite
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
     * @ORM\Column(name="vst_num_pv", type="string", length=255, nullable=true)
     */
    private $vstNumPv;

    /**
     * @var string
     *
     * @ORM\Column(name="vst_num_feuille_caisse", type="string", length=255, nullable=true)
     */
    private $vstNumFeuilleCaisse;

    /**
     * @var string
     *
     * @ORM\Column(name="vst_duree_reparation", type="string", length=100, nullable=true)
     */
    private $vstDureeReparation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vst_is_apte", type="boolean", nullable=false)
     */
    private $vstIsApte = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vst_is_contre_visite", type="boolean", nullable=false)
     */
    private $vstIsContreVisite = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="vst_date_expiration", type="date", nullable=true)
     */
    private $vstDateExpiration;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="vst_created", type="datetime", nullable=true)
     */
    private $vstCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="vst_updated", type="datetime", nullable=true)
     */
    private $vstUpdated;

    /**
     * @var \CtCarteGrise
     *
     * @ORM\ManyToOne(targetEntity="CtCarteGrise", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_carte_grise_id", referencedColumnName="id")
     * })
     */
    private $ctCarteGrise;

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
     * @var \CtTypeVisite
     *
     * @ORM\ManyToOne(targetEntity="CtTypeVisite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_type_visite_id", referencedColumnName="id")
     * })
     */
    private $ctTypeVisite;

    /**
     * @var \CtUsage
     *
     * @ORM\ManyToOne(targetEntity="CtUsage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_usage_id", referencedColumnName="id")
     * })
     */
    private $ctUsage;

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
     * @var \CtUser
     *
     * @ORM\ManyToOne(targetEntity="Ct\Service\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_verificateur_id", referencedColumnName="id")
     * })
     */
    private $ctVerificateur;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="CtAnomalie")
     * @ORM\JoinTable(name="ct_visite_anomalie",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ct_anomalie_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="ct_visite_id", referencedColumnName="id")
     *   }
     * )
     */
    private $ctAnomalie;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="CtVisiteExtra")
     * @ORM\JoinTable(name="ct_visite_visite_extra",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ct_visite_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="ct_visite_extra_id", referencedColumnName="id")
     *   }
     * )
     */
    private $ctVisiteExtra;

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
     * Constructor
     */
    public function __construct()
    {
        $this->vstCreated    = new \DateTime();
        $this->ctAnomalie    = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ctVisiteExtra = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set vstNumPv
     *
     * @param string $vstNumPv
     *
     * @return CtVisite
     */
    public function setVstNumPv($vstNumPv)
    {
        $this->vstNumPv = $vstNumPv;

        return $this;
    }

    /**
     * Get vstNumPv
     *
     * @return string
     */
    public function getVstNumPv()
    {
        return $this->vstNumPv;
    }

    /**
     * Set vstNumFeuilleCaisse
     *
     * @param string $vstNumFeuilleCaisse
     *
     * @return CtVisite
     */
    public function setVstNumFeuilleCaisse($vstNumFeuilleCaisse)
    {
        $this->vstNumFeuilleCaisse = $vstNumFeuilleCaisse;

        return $this;
    }

    /**
     * Get vstNumFeuilleCaisse
     *
     * @return string
     */
    public function getVstNumFeuilleCaisse()
    {
        return $this->vstNumFeuilleCaisse;
    }

    /**
     * Set vstDateExpiration
     *
     * @param \DateTime $vstDateExpiration
     *
     * @return CtVisite
     */
    public function setVstDateExpiration($vstDateExpiration)
    {
        $this->vstDateExpiration = $vstDateExpiration;

        return $this;
    }

    /**
     * Get vstDateExpiration
     *
     * @return \DateTime
     */
    public function getVstDateExpiration()
    {
        return $this->vstDateExpiration;
    }

    /**
     * Set vstCreated
     *
     * @param \DateTime $vstCreated
     *
     * @return CtVisite
     */
    public function setVstCreated($vstCreated)
    {
        $this->vstCreated = $vstCreated;

        return $this;
    }

    /**
     * Get vstCreated
     *
     * @return \DateTime
     */
    public function getVstCreated()
    {
        return $this->vstCreated;
    }

    /**
     * Set vstUpdated
     *
     * @param \DateTime $vstUpdated
     *
     * @return CtVisite
     */
    public function setVstUpdated($vstUpdated)
    {
        $this->vstUpdated = $vstUpdated;

        return $this;
    }

    /**
     * Get vstUpdated
     *
     * @return \DateTime
     */
    public function getVstUpdated()
    {
        return $this->vstUpdated;
    }

    /**
     * Set ctCarteGrise
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtCarteGrise $ctCarteGrise
     *
     * @return CtVisite
     */
    public function setCtCarteGrise(\Ct\Service\MetierManagerBundle\Entity\CtCarteGrise $ctCarteGrise = null)
    {
        $this->ctCarteGrise = $ctCarteGrise;

        return $this;
    }

    /**
     * Get ctCarteGrise
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtCarteGrise
     */
    public function getCtCarteGrise()
    {
        return $this->ctCarteGrise;
    }

    /**
     * Set ctCentre
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtCentre $ctCentre
     *
     * @return CtVisite
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
     * Set ctTypeVisite
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtTypeVisite $ctTypeVisite
     *
     * @return CtVisite
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

    /**
     * Set ctUsage
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtUsage $ctUsage
     *
     * @return CtVisite
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
     * Set ctUser
     *
     * @param \Ct\Service\UserBundle\Entity\User $ctUser
     *
     * @return CtVisite
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
     * Set ctVerificateur
     *
     * @param \Ct\Service\UserBundle\Entity\User $ctVerificateur
     *
     * @return CtVisite
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
     * Add ctAnomalie
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtAnomalie $ctAnomalie
     *
     * @return CtVisite
     */
    public function addCtAnomalie(\Ct\Service\MetierManagerBundle\Entity\CtAnomalie $ctAnomalie)
    {
        $this->ctAnomalie[] = $ctAnomalie;

        return $this;
    }

    /**
     * Remove ctAnomalie
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtAnomalie $ctAnomalie
     */
    public function removeCtAnomalie(\Ct\Service\MetierManagerBundle\Entity\CtAnomalie $ctAnomalie)
    {
        $this->ctAnomalie->removeElement($ctAnomalie);
    }

    /**
     * Get ctAnomalie
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCtAnomalie()
    {
        return $this->ctAnomalie;
    }

    /**
     * Add ctVisiteExtra
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtVisiteExtra $ctVisiteExtra
     *
     * @return CtVisite
     */
    public function addCtVisiteExtra(\Ct\Service\MetierManagerBundle\Entity\CtVisiteExtra $ctVisiteExtra)
    {
        $this->ctVisiteExtra[] = $ctVisiteExtra;

        return $this;
    }

    /**
     * Remove ctVisiteExtra
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtVisiteExtra $ctVisiteExtra
     */
    public function removeCtVisiteExtra(\Ct\Service\MetierManagerBundle\Entity\CtVisiteExtra $ctVisiteExtra)
    {
        $this->ctVisiteExtra->removeElement($ctVisiteExtra);
    }

    /**
     * Get ctVisiteExtra
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCtVisiteExtra()
    {
        return $this->ctVisiteExtra;
    }

    /**
     * Set ctUtilisation
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtUtilisation $ctUtilisation
     *
     * @return CtVisite
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
     * Set vstIsApte
     *
     * @param boolean $vstIsApte
     *
     * @return CtVisite
     */
    public function setVstIsApte($vstIsApte)
    {
        $this->vstIsApte = $vstIsApte;

        return $this;
    }

    /**
     * Get vstIsApte
     *
     * @return boolean
     */
    public function getVstIsApte()
    {
        return $this->vstIsApte;
    }

    /**
     * Set vstIsContreVisite
     *
     * @param boolean $vstIsContreVisite
     *
     * @return CtVisite
     */
    public function setVstIsContreVisite($vstIsContreVisite)
    {
        $this->vstIsContreVisite = $vstIsContreVisite;

        return $this;
    }

    /**
     * Get vstIsContreVisite
     *
     * @return boolean
     */
    public function getVstIsContreVisite()
    {
        return $this->vstIsContreVisite;
    }

    /**
     * Set vstDureeReparation
     *
     * @param string $vstDureeReparation
     *
     * @return CtVisite
     */
    public function setVstDureeReparation($vstDureeReparation)
    {
        $this->vstDureeReparation = $vstDureeReparation;

        return $this;
    }

    /**
     * Get vstDureeReparation
     *
     * @return string
     */
    public function getVstDureeReparation()
    {
        return $this->vstDureeReparation;
    }
}
