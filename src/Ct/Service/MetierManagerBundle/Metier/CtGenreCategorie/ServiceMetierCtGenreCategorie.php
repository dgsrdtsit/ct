<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtGenreCategorie;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtGenreCategorie
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
     * Récuperer le repository genre_categorie
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_GENRE_CATEGORIE);
    }

    /**
     * Récuperer tout les genre_categories
     * @return array
     */
    public function getAllCtGenreCategorie()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les genre_categories par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtGenreCategorieByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un genre_categorie par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtGenreCategorieById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un genre_categorie
     * @param Entity $_genre_categorie
     * @param string $_action
     * @return boolean
     */
    public function saveCtGenreCategorie($_genre_categorie, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_genre_categorie);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un genre_categorie
     * @param Entity $_genre_categorie
     * @return boolean
     */
    public function deleteCtGenreCategorie($_genre_categorie)
    {
        $this->_entity_manager->remove($_genre_categorie);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un genre_categorie
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtGenreCategorie($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_genre_categorie = $this->getCtGenreCategorieById($_id);
                $this->deleteCtGenreCategorie($_genre_categorie);
            }
        }

        return true;
    }
}
