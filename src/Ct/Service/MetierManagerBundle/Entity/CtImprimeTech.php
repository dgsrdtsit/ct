<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *  CtImprimeTech
 *
 *  @ORM\Table(name="ct_imprime_tech")
 *  @ORM\Entity
 *  @UniqueEntity(
 *      fields = {"nomImprimeTech"},
 *      errorPath = "nomImprimeTech",
 *      message = "Cet type d'imprimé est déjà dans la liste."
 *  )
 */
class CtImprimeTech
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
     * @ORM\Column(name="nom_imprime_tech", type="string", length=128, nullable=false, unique=true)
     */
    private $nomImprimeTech;

    /**
     * @var string
     *
     * @ORM\Column(name="ute_imprime_tech", type="string", length=64)
     */
    private $uteImprimeTech;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="prtt_created_at", type="datetime", nullable=true)
     */
    private $prttCreatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="prtt_updated_at", type="datetime", nullable=true)
     */
    private $prttUpdatedAt;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nomImprimeTech
     *
     * @param string $nomImprimeTech
     *
     * @return CtImprimeTech
     */
    public function setNomImprimeTech($nomImprimeTech)
    {
        $this->nomImprimeTech = $nomImprimeTech;

        return $this;
    }

    /**
     * Get nomImprimeTech
     *
     * @return string
     */
    public function getNomImprimeTech()
    {
        return $this->nomImprimeTech;
    }

    /**
     * Set uteImprimeTech
     *
     * @param string $uteImprimeTech
     *
     * @return CtImprimeTech
     */
    public function setUteImprimeTech($uteImprimeTech)
    {
        $this->uteImprimeTech = $uteImprimeTech;

        return $this;
    }

    /**
     * Get uteImprimeTech
     *
     * @return string
     */
    public function getUteImprimeTech()
    {
        return $this->uteImprimeTech;
    }

    /**
     * Set prttCreatedAt
     *
     * @param \DateTime $prttCreatedAt
     *
     * @return CtImprimeTech
     */
    public function setPrttCreatedAt($prttCreatedAt)
    {
        $this->prttCreatedAt = $prttCreatedAt;

        return $this;
    }

    /**
     * Get prttCreatedAt
     *
     * @return \DateTime
     */
    public function getPrttCreatedAt()
    {
        return $this->prttCreatedAt;
    }

    /**
     * Set prttUpdatedAt
     *
     * @param \DateTime $prttUpdatedAt
     *
     * @return CtImprimeTech
     */
    public function setPrttUpdatedAt($prttUpdatedAt)
    {
        $this->prttUpdatedAt = $prttUpdatedAt;

        return $this;
    }

    /**
     * Get prttUpdatedAt
     *
     * @return \DateTime
     */
    public function getPrttUpdatedAt()
    {
        return $this->prttUpdatedAt;
    }

    /**
     * Set ctUser
     *
     * @param \Ct\Service\UserBundle\Entity\User $ctUser
     *
     * @return CtImprimeTech
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
}
