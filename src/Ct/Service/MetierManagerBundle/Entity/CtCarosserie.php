<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtCarosserie
 *
 * @ORM\Table(name="ct_carosserie")
 * @ORM\Entity
 */
class CtCarosserie
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
     * @ORM\Column(name="crs_libelle", type="string", length=255, nullable=true)
     */
    private $crsLibelle;



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
     * Set crsLibelle
     *
     * @param string $crsLibelle
     *
     * @return CtCarosserie
     */
    public function setCrsLibelle($crsLibelle)
    {
        $this->crsLibelle = $crsLibelle;

        return $this;
    }

    /**
     * Get crsLibelle
     *
     * @return string
     */
    public function getCrsLibelle()
    {
        return $this->crsLibelle;
    }
}
