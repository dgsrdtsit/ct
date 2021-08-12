<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtMarque
 *
 * @ORM\Table(name="ct_zone_deserte")
 * @ORM\Entity
 */
class CtZoneDeserte
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
     * @ORM\Column(name="zd_libelle", type="string", length=255, nullable=true)
     */
    private $zdLibelle;


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
     * @return string
     */
    public function getZdLibelle()
    {
        return $this->zdLibelle;
    }

    /**
     * @param string $zdLibelle
     */
    public function setZdLibelle($zdLibelle)
    {
        $this->zdLibelle = $zdLibelle;
    }
}
