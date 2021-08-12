<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtGenreTarif;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtGenreTarif
{
    private $_entity_manager;
    private $_container;

    public function __construct(EntityManager $_entity_manager, Container $_container)
    {
        $this->_entity_manager = $_entity_manager;
        $this->_container      = $_container;
    }

    /**
     * Ajouter un message flash
     * @param string $_type
     * @param string $_message
     * @return mixed
     */
    public function setFlash($_type, $_message) {
        return $this->_container->get('session')->getFlashBag()->add($_type, $_message);
    }

    /**
     * Récuperer le repository genreTarif
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_GENRE_TARIF);
    }

    /**
     * Récuperer tout les genreTarifs
     * @return array
     */
    public function getAllCtGenreTarif()
    {
        return $this->getRepository()->findAll();
    }


    /**
     * Récuperer le tarif par genre
     * @return array
     */
    public function getAllCtGenreTarifByGenre()
    {

        $_entity_tarif = EntityName::CT_GENRE_TARIF;
        $_entity_genre = EntityName::CT_GENRE;
        /*$_dql    = "SELECT g.grLibelle as grLibelle, g.grCode as grCode, t.id as id,
                    t.grtPrix as grtPrix, t.grtAnnee as grtAnnee
                    FROM $_entity_tarif t 
                    LEFT JOIN t.ctGenre g
                    WHERE YEAR(t.grtAnnee) = :annee
                    AND t.grtAnnee = (SELECT MAX(t2.grtAnnee) FROM $_entity_tarif t2 LEFT JOIN t2.ctGenre g2 WHERE g2.id = g.id)
                    ";*/
        $_dql    = "SELECT g.grLibelle as grLibelle, g.grCode as grCode, t.id as id,
                    t.grtPrix as grtPrix, t.grtAnnee as grtAnnee
                    FROM $_entity_tarif t 
                    LEFT JOIN t.ctGenre g
                    ";
        $_query  = $this->_entity_manager->createQuery($_dql);
        return $_query->getResult();
    }


    /**
     * Récuperer tout les genreTarifs par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtGenreTarifByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un genreTarif par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtGenreTarifById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un genreTarif
     * @param Entity $_genreTarif
     * @param string $_action
     * @return boolean
     */
    public function saveCtGenreTarif($_genreTarif, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_genreTarif);
        }
        try {
            $this->_entity_manager->flush();
        } catch (\Exception $e) {
            $errorMessage = "Ce genre a deja un tarif";
            $this->_container->get('session')->getFlashBag()->add('error', $errorMessage);
            return false;
        }

        return true;
    }

    /**
     * Supprimer un genreTarif
     * @param Entity $_genreTarif
     * @return boolean
     */
    public function deleteCtGenreTarif($_genreTarif)
    {
        $this->_entity_manager->remove($_genreTarif);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un genreTarif
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtGenreTarif($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_genreTarif = $this->getCtGenreTarifById($_id);
                $this->deleteCtGenreTarif($_genreTarif);
            }
        }

        return true;
    }
}
