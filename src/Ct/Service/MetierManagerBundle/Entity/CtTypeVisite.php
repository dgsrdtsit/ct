<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtTypeVisite
 *
 * @ORM\Table(name="ct_type_visite")
 * @ORM\Entity
 */
class CtTypeVisite
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
     * @ORM\Column(name="tpv_libelle", type="string", length=45, nullable=true)
     */
    private $tpvLibelle;

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
     * Set tpvLibelle
     *
     * @param string $tpvLibelle
     *
     * @return CtTypeVisite
     */
    public function setTpvLibelle($tpvLibelle)
    {
        $this->tpvLibelle = $tpvLibelle;

        return $this;
    }

    /**
     * Get tpvLibelle
     *
     * @return string
     */
    public function getTpvLibelle()
    {
        return $this->tpvLibelle;
    }
}
