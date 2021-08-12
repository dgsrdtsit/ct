<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtProvince
 *
 * @ORM\Table(name="ct_province")
 * @ORM\Entity
 */
class CtProvince
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
     * @ORM\Column(name="prv_nom", type="string", length=255, nullable=true)
     */
    private $prvNom;

    /**
     * @var string
     *
     * @ORM\Column(name="prv_code", type="string", length=255, nullable=true)
     */
    private $prvCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="prv_created_at", type="datetime", nullable=true)
     */
    private $prvCreatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="prv_updated_at", type="datetime", nullable=true)
     */
    private $prvUpdatedAt;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prvCreatedAt = new \DateTime();
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
     * Set prvNom
     *
     * @param string $prvNom
     *
     * @return CtProvince
     */
    public function setPrvNom($prvNom)
    {
        $this->prvNom = $prvNom;

        return $this;
    }

    /**
     * Get prvNom
     *
     * @return string
     */
    public function getPrvNom()
    {
        return $this->prvNom;
    }

    /**
     * Set prvCode
     *
     * @param string $prvCode
     *
     * @return CtProvince
     */
    public function setPrvCode($prvCode)
    {
        $this->prvCode = $prvCode;

        return $this;
    }

    /**
     * Get prvCode
     *
     * @return string
     */
    public function getPrvCode()
    {
        return $this->prvCode;
    }

    /**
     * Set prvCreatedAt
     *
     * @param \DateTime $prvCreatedAt
     *
     * @return CtProvince
     */
    public function setPrvCreatedAt($prvCreatedAt)
    {
        $this->prvCreatedAt = $prvCreatedAt;

        return $this;
    }

    /**
     * Get prvCreatedAt
     *
     * @return \DateTime
     */
    public function getPrvCreatedAt()
    {
        return $this->prvCreatedAt;
    }

    /**
     * Set prvUpdatedAt
     *
     * @param \DateTime $prvUpdatedAt
     *
     * @return CtProvince
     */
    public function setPrvUpdatedAt($prvUpdatedAt)
    {
        $this->prvUpdatedAt = $prvUpdatedAt;

        return $this;
    }

    /**
     * Get prvUpdatedAt
     *
     * @return \DateTime
     */
    public function getPrvUpdatedAt()
    {
        return $this->prvUpdatedAt;
    }
}
