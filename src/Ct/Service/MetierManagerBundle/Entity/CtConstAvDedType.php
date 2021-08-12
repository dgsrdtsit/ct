<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtConstAvDedType
 *
 * @ORM\Table(name="ct_const_av_ded_type")
 * @ORM\Entity
 */
class CtConstAvDedType
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
     * @ORM\Column(name="cad_tp_libelle", type="string", length=45, nullable=true)
     */
    private $cadTpLibelle;



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
     * Set cadTpLibelle
     *
     * @param string $cadTpLibelle
     *
     * @return CtConstAvDedType
     */
    public function setCadTpLibelle($cadTpLibelle)
    {
        $this->cadTpLibelle = $cadTpLibelle;

        return $this;
    }

    /**
     * Get cadTpLibelle
     *
     * @return string
     */
    public function getCadTpLibelle()
    {
        return $this->cadTpLibelle;
    }
}
