<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtMarque
 *
 * @ORM\Table(name="ct_marque")
 * @ORM\Entity
 */
class CtMarque
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
     * @ORM\Column(name="mrq_libelle", type="string", length=255, nullable=true)
     */
    private $mrqLibelle;



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
     * Set mrqLibelle
     *
     * @param string $mrqLibelle
     *
     * @return CtMarque
     */
    public function setMrqLibelle($mrqLibelle)
    {
        $this->mrqLibelle = $mrqLibelle;

        return $this;
    }

    /**
     * Get mrqLibelle
     *
     * @return string
     */
    public function getMrqLibelle()
    {
        return $this->mrqLibelle;
    }
}
