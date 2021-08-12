<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtTypeReception
 *
 * @ORM\Table(name="ct_type_reception")
 * @ORM\Entity
 */
class CtTypeReception
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
     * @ORM\Column(name="tprcp_libelle", type="string", length=45, nullable=true)
     */
    private $tprcpLibelle;



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
     * Set tprcpLibelle
     *
     * @param string $tprcpLibelle
     *
     * @return CtTypeReception
     */
    public function setTprcpLibelle($tprcpLibelle)
    {
        $this->tprcpLibelle = $tprcpLibelle;

        return $this;
    }

    /**
     * Get tprcpLibelle
     *
     * @return string
     */
    public function getTprcpLibelle()
    {
        return $this->tprcpLibelle;
    }
}
