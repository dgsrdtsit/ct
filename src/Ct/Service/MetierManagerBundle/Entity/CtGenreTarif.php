<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtGenreTarif
 *
 * @ORM\Table(name="ct_genre_tarif", indexes={@ORM\Index(name="fk_ct_genre_tarif_ct_genre1_idx", columns={"ct_genre_id"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="uk_ct_genre_ct_annee", columns={"grt_annee", "ct_genre_id"})})
 * @ORM\Entity
 */
class CtGenreTarif
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
     * @var float
     *
     * @ORM\Column(name="grt_prix", type="float", length=25, nullable=true)
     */
    private $grtPrix;

    /**
     * @var string
     *
     * @ORM\Column(name="grt_annee", type="string", length=4, nullable=true)
     */
    private $grtAnnee;

    /**
     * @var \CtGenre
     *
     * @ORM\ManyToOne(targetEntity="CtGenre")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ct_genre_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $ctGenre;



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
     * Set grtPrix
     *
     * @param float $grtPrix
     *
     * @return CtGenreTarif
     */
    public function setGrtPrix($grtPrix)
    {
        $this->grtPrix = $grtPrix;

        return $this;
    }

    /**
     * Get grtPrix
     *
     * @return float
     */
    public function getGrtPrix()
    {
        return $this->grtPrix;
    }

    

    /**
     * Set ctGenre
     *
     * @param \Ct\Service\MetierManagerBundle\Entity\CtGenre $ctGenre
     *
     * @return CtGenreTarif
     */
    public function setCtGenre(\Ct\Service\MetierManagerBundle\Entity\CtGenre $ctGenre = null)
    {
        $this->ctGenre = $ctGenre;

        return $this;
    }

    /**
     * Get ctGenre
     *
     * @return \Ct\Service\MetierManagerBundle\Entity\CtGenre
     */
    public function getCtGenre()
    {
        return $this->ctGenre;
    }



    /**
     * Set grtAnnee
     *
     * @param string $grtAnnee
     *
     * @return CtGenreTarif
     */
    public function setGrtAnnee($grtAnnee)
    {
        $this->grtAnnee = $grtAnnee;

        return $this;
    }

    /**
     * Get grtAnnee
     *
     * @return string
     */
    public function getGrtAnnee()
    {
        return $this->grtAnnee;
    }
}
