<?php

namespace Ct\Service\MetierManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CtGenreCategorie
 *
 * @ORM\Table(name="ct_genre_categorie")
 * @ORM\Entity
 */
class CtGenreCategorie
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
     * @ORM\Column(name="gc_libelle", type="string", length=255, nullable=true)
     */
    private $gcLibelle;

    /**
     * @var bool
     *
     * @ORM\Column(name="gc_is_calculable", type="boolean", nullable=false)
     */
    private $gcIsCalculable = false;


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
     * Set gcLibelle
     *
     * @param string $gcLibelle
     *
     * @return CtGenreCategorie
     */
    public function setGcLibelle($gcLibelle)
    {
        $this->gcLibelle = $gcLibelle;

        return $this;
    }

    /**
     * Get gcLibelle
     *
     * @return string
     */
    public function getGcLibelle()
    {
        return $this->gcLibelle;
    }

    /**
     * Set gcIsCalculable
     *
     * @param boolean $gcIsCalculable
     *
     * @return CtGenreCategorie
     */
    public function setGcIsCalculable($gcIsCalculable)
    {
        $this->gcIsCalculable = $gcIsCalculable;

        return $this;
    }

    /**
     * Get gcIsCalculable
     *
     * @return bool
     */
    public function getGcIsCalculable()
    {
        return $this->gcIsCalculable;
    }
}

