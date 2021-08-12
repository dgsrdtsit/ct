<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtCentre
 *
 * @ORM\Table(name="ct_centre", indexes={@ORM\Index(name="fk_ct_centre_ct_province1_idx", columns={"ct_province_id"})})
 * @ORM\Entity
 */
class CtCentre
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
     * @ORM\Column(name="ctr_nom", type="string", length=255, nullable=true)
     */
    private $ctrNom;

    /**
     * @var string
     *
     * @ORM\Column(name="ctr_code", type="string", length=255, nullable=true)
     */
    private $ctrCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ctr_created_at", type="datetime", nullable=true)
     */
    private $ctrCreatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ctr_updated_at", type="datetime", nullable=true)
     */
    private $ctrUpdatedAt;

    /**
     * @var \CtProvince
     *
     * @ORM\ManyToOne(targetEntity="CtProvince")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_province_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $ctProvince;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ctrCreatedAt = new \DateTime();
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
     * Set ctrNom
     *
     * @param string $ctrNom
     *
     * @return CtCentre
     */
    public function setCtrNom($ctrNom)
    {
        $this->ctrNom = $ctrNom;

        return $this;
    }

    /**
     * Get ctrNom
     *
     * @return string
     */
    public function getCtrNom()
    {
        return $this->ctrNom;
    }

    /**
     * Set ctrCode
     *
     * @param string $ctrCode
     *
     * @return CtCentre
     */
    public function setCtrCode($ctrCode)
    {
        $this->ctrCode = $ctrCode;

        return $this;
    }

    /**
     * Get ctrCode
     *
     * @return string
     */
    public function getCtrCode()
    {
        return $this->ctrCode;
    }

    /**
     * Set ctrCreatedAt
     *
     * @param \DateTime $ctrCreatedAt
     *
     * @return CtCentre
     */
    public function setCtrCreatedAt($ctrCreatedAt)
    {
        $this->ctrCreatedAt = $ctrCreatedAt;

        return $this;
    }

    /**
     * Get ctrCreatedAt
     *
     * @return \DateTime
     */
    public function getCtrCreatedAt()
    {
        return $this->ctrCreatedAt;
    }

    /**
     * Set ctrUpdatedAt
     *
     * @param \DateTime $ctrUpdatedAt
     *
     * @return CtCentre
     */
    public function setCtrUpdatedAt($ctrUpdatedAt)
    {
        $this->ctrUpdatedAt = $ctrUpdatedAt;

        return $this;
    }

    /**
     * Get ctrUpdatedAt
     *
     * @return \DateTime
     */
    public function getCtrUpdatedAt()
    {
        return $this->ctrUpdatedAt;
    }

    /**
     * Set ctProvince
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtProvince $ctProvince
     *
     * @return CtCentre
     */
    public function setCtProvince(\Ct\Service\MetierManagerBundle\Entity\CtProvince $ctProvince = null)
    {
        $this->ctProvince = $ctProvince;

        return $this;
    }

    /**
     * Get ctProvince
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtProvince
     */
    public function getCtProvince()
    {
        return $this->ctProvince;
    }
}
