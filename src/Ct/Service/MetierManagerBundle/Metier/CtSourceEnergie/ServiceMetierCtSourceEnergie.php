<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtSourceEnergie;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtSourceEnergie
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
     * Récuperer le repository Anomalie
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_ENERGIE);
    }

    /**
     * Récuperer tout les Anomalies
     * @return array
     */
    public function getAllCtSourceEnergie()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les anomalies par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtSourceEnergieByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer une anomalie par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtSourceEnergieById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un Anomalie
     * @param Entity $_anomalie
     * @param string $_action
     * @return boolean
     */
    public function saveCtSourceEnergie($_anomalie, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_anomalie);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un Anomalie
     * @param Entity $_anomalie
     * @return boolean
     */
    public function deleteCtSourceEnergie($_anomalie)
    {
        $this->_entity_manager->remove($_anomalie);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un Anomalie
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtSourceEnergie($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_anomalie = $this->getCtSourceEnergieById($_id);
                $this->deleteCtSourceEnergie($_anomalie);
            }
        }

        return true;
    }
}
