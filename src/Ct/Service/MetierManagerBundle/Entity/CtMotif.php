<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtMotif
 *
 * @ORM\Table(name="ct_motif")
 * @ORM\Entity
 */
class CtMotif
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
     * @ORM\Column(name="mtf_libelle", type="string", length=255, nullable=true)
     */
    private $mtfLibelle;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mtf_is_calculable", type="boolean", nullable=false)
     */
    private $mtfIsCalculable = false;


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
     * Set mtfLibelle
     *
     * @param string $mtfLibelle
     *
     * @return CtMotif
     */
    public function setMtfLibelle($mtfLibelle)
    {
        $this->mtfLibelle = $mtfLibelle;

        return $this;
    }

    /**
     * Get mtfLibelle
     *
     * @return string
     */
    public function getMtfLibelle()
    {
        return $this->mtfLibelle;
    }

    /**
     * Set mtfIsCalculable
     *
     * @param boolean $mtfIsCalculable
     *
     * @return CtMotif
     */
    public function setMtfIsCalculable($mtfIsCalculable)
    {
        $this->mtfIsCalculable = $mtfIsCalculable;

        return $this;
    }

    /**
     * Get mtfIsCalculable
     *
     * @return boolean
     */
    public function getMtfIsCalculable()
    {
        return $this->mtfIsCalculable;
    }
}
