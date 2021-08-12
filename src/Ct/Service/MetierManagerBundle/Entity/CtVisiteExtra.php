<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtVisiteExtra
 *
 * @ORM\Table(name="ct_visite_extra")
 * @ORM\Entity
 */
class CtVisiteExtra
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
     * @ORM\Column(name="vste_libelle", type="string", length=255, nullable=true)
     */
    private $vsteLibelle;


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
     * Add ctVisite
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtVisite $ctVisite
     *
     * @return CtVisiteExtra
     */
    public function addCtVisite(\Ct\Service\MetierManagerBundle\Entity\CtVisite $ctVisite)
    {
        $this->ctVisite[] = $ctVisite;

        return $this;
    }

    /**
     * Remove ctVisite
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtVisite $ctVisite
     */
    public function removeCtVisite(\Ct\Service\MetierManagerBundle\Entity\CtVisite $ctVisite)
    {
        $this->ctVisite->removeElement($ctVisite);
    }

    /**
     * Get ctVisite
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCtVisite()
    {
        return $this->ctVisite;
    }

    /**
     * Set vsteLibelle
     *
     * @param string $vsteLibelle
     *
     * @return CtVisiteExtra
     */
    public function setVsteLibelle($vsteLibelle)
    {
        $this->vsteLibelle = $vsteLibelle;

        return $this;
    }

    /**
     * Get vsteLibelle
     *
     * @return string
     */
    public function getVsteLibelle()
    {
        return $this->vsteLibelle;
    }
}
