<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtTypeDroitPtac;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtTypeDroitPtac
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
     * Récuperer le repository type de droit_ptac
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_TYPE_DROIT_PTAC);
    }

    /**
     * Récuperer tout les types de droit_ptac
     * @return array
     */
    public function getAllCtTypeDroitPtac()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les types de droit_ptac par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtTypeDroitPtacByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un type de droit_ptac par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtTypeDroitPtacById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer une type de droit_ptac
     * @param Entity $_type_droit_ptac
     * @param string $_action
     * @return boolean
     */
    public function saveCtTypeDroitPtac($_type_droit_ptac, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_type_droit_ptac);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer une type de droit_ptac
     * @param Entity $_type_droit_ptac
     * @return boolean
     */
    public function deleteCtTypeDroitPtac($_type_droit_ptac)
    {
        $this->_entity_manager->remove($_type_droit_ptac);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un type de droit_ptac
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtTypeDroitPtac($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_type_droit_ptac = $this->getCtTypeDroitPtacById($_id);
                $this->deleteCtTypeDroitPtac($_type_droit_ptac);
            }
        }

        return true;
    }
}
