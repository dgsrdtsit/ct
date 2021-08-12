<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtCentre;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtCentre
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
     * Récuperer le repository centre
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_CENTRE);
    }

    /**
     * Récuperer tout les centres
     * @return array
     */
    public function getAllCtCentre()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les anomalies par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtCentreByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer une anomalie par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtCentreById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Modifier un centre
     * @param Entity $_centre
     * @return boolean
     */
    public function updateCtCentre($_centre)
    {
        $_centre->setCtrUpdatedAt(new \DateTime());

        $this->saveCtCentre($_centre, 'update');
    }

    /**
     * Enregistrer un centre
     * @param Entity $_centre
     * @param string $_action
     * @return boolean
     */
    public function saveCtCentre($_centre, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_centre);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un centre
     * @param Entity $_centre
     * @return boolean
     */
    public function deleteCtCentre($_centre)
    {
        $this->_entity_manager->remove($_centre);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un centre
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtCentre($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_centre = $this->getCtCentreById($_id);
                $this->deleteCtCentre($_centre);
            }
        }

        return true;
    }
}
