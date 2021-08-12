<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtTypeDroitPtac
 *
 * @ORM\Table(name="ct_type_droit_ptac")
 * @ORM\Entity
 */
class CtTypeDroitPtac
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
     * @ORM\Column(name="tp_dp_libelle", type="string", length=45, nullable=true)
     */
    private $tpDpLibelle;



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
     * Set tpDpLibelle
     *
     * @param string $tpDpLibelle
     *
     * @return CtTypeDroitPtac
     */
    public function setTpDpLibelle($tpDpLibelle)
    {
        $this->tpDpLibelle = $tpDpLibelle;

        return $this;
    }

    /**
     * Get tpDpLibelle
     *
     * @return string
     */
    public function getTpDpLibelle()
    {
        return $this->tpDpLibelle;
    }
}
