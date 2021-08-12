<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtProcesVerbal
 *
 * @ORM\Table(name="ct_proces_verbal")
 * @ORM\Entity
 */
class CtProcesVerbal
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="pv_type", type="string", length=255, nullable=true)
     */
    private $pvType;

    /**
     * @var float
     *
     * @ORM\Column(name="pv_tarif", type="float", precision=10, scale=0, nullable=true)
     */
    private $pvTarif;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pvType
     *
     * @param string $pvType
     *
     * @return CtProcesVerbal
     */
    public function setPvType($pvType)
    {
        $this->pvType = $pvType;

        return $this;
    }

    /**
     * Get pvType
     *
     * @return string
     */
    public function getPvType()
    {
        return $this->pvType;
    }

    /**
     * Set pvTarif
     *
     * @param float $pvTarif
     *
     * @return CtProcesVerbal
     */
    public function setPvTarif($pvTarif)
    {
        $this->pvTarif = $pvTarif;

        return $this;
    }

    /**
     * Get pvTarif
     *
     * @return float
     */
    public function getPvTarif()
    {
        return $this->pvTarif;
    }
}
