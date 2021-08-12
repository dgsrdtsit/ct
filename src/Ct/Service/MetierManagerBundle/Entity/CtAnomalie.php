<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtAnomalie
 *
 * @ORM\Table(name="ct_anomalie", indexes={@ORM\Index(name="fk_ct_anomalie_ct_anomalie_type1_idx", columns={"ct_anomalie_type_id"})})
 * @ORM\Entity
 */
class CtAnomalie
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
     * @ORM\Column(name="anml_libelle", type="string", length=100, nullable=true)
     */
    private $anmlLibelle;

    /**
     * @var string
     *
     * @ORM\Column(name="anml_code", type="string", length=10, nullable=true)
     */
    private $anmlCode;

    /**
     * @var \CtAnomalieType
     *
     * @ORM\ManyToOne(targetEntity="CtAnomalieType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_anomalie_type_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $ctAnomalieType;


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
     * Set anmlLibelle
     *
     * @param string $anmlLibelle
     *
     * @return CtAnomalie
     */
    public function setAnmlLibelle($anmlLibelle)
    {
        $this->anmlLibelle = $anmlLibelle;

        return $this;
    }

    /**
     * Get anmlLibelle
     *
     * @return string
     */
    public function getAnmlLibelle()
    {
        return $this->anmlLibelle;
    }

    /**
     * Set anmlCode
     *
     * @param string $anmlCode
     *
     * @return CtAnomalie
     */
    public function setAnmlCode($anmlCode)
    {
        $this->anmlCode = $anmlCode;

        return $this;
    }

    /**
     * Get anmlCode
     *
     * @return string
     */
    public function getAnmlCode()
    {
        return $this->anmlCode;
    }

    /**
     * Set ctAnomalieType
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtAnomalieType $ctAnomalieType
     *
     * @return CtAnomalie
     */
    public function setCtAnomalieType(\Ct\Service\MetierManagerBundle\Entity\CtAnomalieType $ctAnomalieType = null)
    {
        $this->ctAnomalieType = $ctAnomalieType;

        return $this;
    }

    /**
     * Get ctAnomalieType
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtAnomalieType
     */
    public function getCtAnomalieType()
    {
        return $this->ctAnomalieType;
    }
}
