<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtTypeAnomalie;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtTypeAnomalie
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
        return $this->_entity_manager->getRepository(EntityName::CT_TYPE_ANOMALIE);
    }

    /**
     * Récuperer tout les Anomalies
     * @return array
     */
    public function getAllCtTypeAnomalie()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les anomalies par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtTypeAnomalieByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer une anomalie par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtTypeAnomalieById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer une Anomalie
     * @param Entity $_type_anomalie
     * @param string $_action
     * @return boolean
     */
    public function saveCtTypeAnomalie($_type_anomalie, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_type_anomalie);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer une Anomalie
     * @param Entity $_type_anomalie
     * @return boolean
     */
    public function deleteCtTypeAnomalie($_type_anomalie)
    {
        $this->_entity_manager->remove($_type_anomalie);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un Anomalie
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtTypeAnomalie($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_anomalie = $this->getCtTypeAnomalieById($_id);
                $this->deleteCtTypeAnomalie($_anomalie);
            }
        }

        return true;
    }
}
