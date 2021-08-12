<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtTypeUsage;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtTypeUsage
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
     * Récuperer le repository type d'usage
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_TYPE_USAGE);
    }

    /**
     * Récuperer tout les types d'usage
     * @return array
     */
    public function getAllCtTypeUsage()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les types d'usage par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtTypeUsageByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un type d'usage par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtTypeUsageById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer une type d'usage
     * @param Entity $_type_usage
     * @param string $_action
     * @return boolean
     */
    public function saveCtTypeUsage($_type_usage, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_type_usage);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer une type d'usage
     * @param Entity $_type_usage
     * @return boolean
     */
    public function deleteCtTypeUsage($_type_usage)
    {
        $this->_entity_manager->remove($_type_usage);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un type d'usage
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtTypeUsage($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_type_usage = $this->getCtTypeUsageById($_id);
                $this->deleteCtTypeUsage($_type_usage);
            }
        }

        return true;
    }
}
