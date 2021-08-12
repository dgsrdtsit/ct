<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtVisiteExtra;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtVisiteExtra
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
     * Récuperer le repository visite_extra
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_VISITE_EXTRA);
    }

    /**
     * Récuperer tout les visite_extras
     * @return array
     */
    public function getAllCtVisiteExtra()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les visite_extras par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtVisiteExtraByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un visite_extra par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtVisiteExtraById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un visite_extra
     * @param Entity $_visite_extra
     * @param string $_action
     * @return boolean
     */
    public function saveCtVisiteExtra($_visite_extra, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_visite_extra);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un visite_extra
     * @param Entity $_visite_extra
     * @return boolean
     */
    public function deleteCtVisiteExtra($_visite_extra)
    {
        $this->_entity_manager->remove($_visite_extra);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un visite_extra
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtVisiteExtra($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_visite_extra = $this->getCtVisiteExtraById($_id);
                $this->deleteCtVisiteExtra($_visite_extra);
            }
        }

        return true;
    }
}
