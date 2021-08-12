<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtSourceEnergie
 *
 * @ORM\Table(name="ct_source_energie")
 * @ORM\Entity
 */
class CtSourceEnergie
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
     * @ORM\Column(name="sre_libelle", type="string", length=255, nullable=true)
     */
    private $sreLibelle;



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
     * Set sreLibelle
     *
     * @param string $sreLibelle
     *
     * @return CtSourceEnergie
     */
    public function setSreLibelle($sreLibelle)
    {
        $this->sreLibelle = $sreLibelle;

        return $this;
    }

    /**
     * Get sreLibelle
     *
     * @return string
     */
    public function getSreLibelle()
    {
        return $this->sreLibelle;
    }
}
