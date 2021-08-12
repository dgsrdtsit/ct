<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtTypeVisite;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtTypeVisite
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
     * Récuperer le repository type de visite
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_TYPE_VISITE);
    }

    /**
     * Récuperer tout les types de visite
     * @return array
     */
    public function getAllCtTypeVisite()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les types de visite par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtTypeVisiteByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un type de visite par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtTypeVisiteById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer une type de visite
     * @param Entity $_type_visite
     * @param string $_action
     * @return boolean
     */
    public function saveCtTypeVisite($_type_visite, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_type_visite);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer une type de visite
     * @param Entity $_type_visite
     * @return boolean
     */
    public function deleteCtTypeVisite($_type_visite)
    {
        $this->_entity_manager->remove($_type_visite);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un type de visite
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtTypeVisite($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_type_visite = $this->getCtTypeVisiteById($_id);
                $this->deleteCtTypeVisite($_type_visite);
            }
        }

        return true;
    }
}
