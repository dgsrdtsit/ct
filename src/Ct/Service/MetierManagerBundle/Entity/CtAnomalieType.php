<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtAnomalieType
 *
 * @ORM\Table(name="ct_anomalie_type")
 * @ORM\Entity
 */
class CtAnomalieType
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
     * @ORM\Column(name="atp_libelle", type="string", length=45, nullable=true)
     */
    private $atpLibelle;



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
     * Set atpLibelle
     *
     * @param string $atpLibelle
     *
     * @return CtAnomalieType
     */
    public function setAtpLibelle($atpLibelle)
    {
        $this->atpLibelle = $atpLibelle;

        return $this;
    }

    /**
     * Get atpLibelle
     *
     * @return string
     */
    public function getAtpLibelle()
    {
        return $this->atpLibelle;
    }
}
