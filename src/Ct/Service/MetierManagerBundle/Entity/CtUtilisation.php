<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtUtilisation
 *
 * @ORM\Table(name="ct_utilisation")
 * @ORM\Entity
 */
class CtUtilisation
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
     * @ORM\Column(name="ut_libelle", type="string", length=45, nullable=true)
     */
    private $utLibelle;



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
     * Set utLibelle
     *
     * @param string $utLibelle
     *
     * @return CtUtilisation
     */
    public function setUtLibelle($utLibelle)
    {
        $this->utLibelle = $utLibelle;

        return $this;
    }

    /**
     * Get utLibelle
     *
     * @return string
     */
    public function getUtLibelle()
    {
        return $this->utLibelle;
    }
}
