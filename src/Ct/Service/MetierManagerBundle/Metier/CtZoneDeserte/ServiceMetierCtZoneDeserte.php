<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtZoneDeserte;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtZoneDeserte
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
     * Récuperer le repository zone_deserte
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_ZONE_DESERTE);
    }

    /**
     * Récuperer tout les zone_desertes
     * @return array
     */
    public function getAllCtZoneDeserte()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les zone_desertes par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtZoneDeserteByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un zone_deserte par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtZoneDeserteById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un zone_deserte
     * @param Entity $_zone_deserte
     * @param string $_action
     * @return boolean
     */
    public function saveCtZoneDeserte($_zone_deserte, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_zone_deserte);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un zone_deserte
     * @param Entity $_zone_deserte
     * @return boolean
     */
    public function deleteCtZoneDeserte($_zone_deserte)
    {
        $this->_entity_manager->remove($_zone_deserte);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un zone_deserte
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtZoneDeserte($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_zone_deserte = $this->getCtZoneDeserteById($_id);
                $this->deleteCtZoneDeserte($_zone_deserte);
            }
        }

        return true;
    }
}
