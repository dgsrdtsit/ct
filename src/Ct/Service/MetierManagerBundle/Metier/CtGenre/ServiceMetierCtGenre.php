<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtGenre;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtGenre
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
     * Récuperer le repository genre
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_GENRE);
    }

    /**
     * Récuperer tout les genres
     * @return array
     */
    public function getAllCtGenre()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les genres par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtGenreByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un genre par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtGenreById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un genre
     * @param Entity $_genre
     * @param string $_action
     * @return boolean
     */
    public function saveCtGenre($_genre, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_genre);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un genre
     * @param Entity $_genre
     * @return boolean
     */
    public function deleteCtGenre($_genre)
    {
        $this->_entity_manager->remove($_genre);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un genre
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtGenre($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_genre = $this->getCtGenreById($_id);
                $this->deleteCtGenre($_genre);
            }
        }

        return true;
    }
}
