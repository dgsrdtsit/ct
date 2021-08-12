<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtUsage;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtUsage
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
     * Récuperer le repository usage
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_USAGE);
    }

    /**
     * Récuperer tout les usages
     * @return array
     */
    public function getAllCtUsage()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les usages par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtUsageByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un usage par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtUsageById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Récuperer usages par type visite
     * @param Integer $_type_visite_id
     * @return array
     */
    public function getCtUsageByTypeVisite($_type_visite_id)
    {
        $_entity_u = EntityName::CT_USAGE;
        $_entity_ut = EntityName::CT_USAGE_TARIF;

        $_dql    = "SELECT u FROM $_entity_u u
                    INNER JOIN $_entity_ut ut
                    WITH ut.ctUsage = u.id
                    WHERE ut.ctTypeVisite = ?1
                    ";
        $_query  = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter(1, $_type_visite_id);
        return $_query->getResult();
    }

    /**
     * Récuperer validite d'une usage
     * @param Integer $_type_visite_id
     * @return array
     */
    public function getValiditeOfUsage($_usage_id)
    {
        $_entity_u = EntityName::CT_USAGE;
        $_entity_ut = EntityName::CT_USAGE_TARIF;

        $_dql    = "SELECT u FROM $_entity_u u
                    INNER JOIN $_entity_ut ut
                    WITH ut.ctUsage = u.id
                    WHERE ut.ctTypeVisite = ?1
                    ";
        $_query  = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter(1, $_usage_id);
        return $_query->getResult();
    }

    /**
     * Enregistrer un usage
     * @param Entity $_usage
     * @param string $_action
     * @return boolean
     */
    public function saveCtUsage($_usage, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_usage);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un usage
     * @param Entity $_usage
     * @return boolean
     */
    public function deleteCtUsage($_usage)
    {
        $this->_entity_manager->remove($_usage);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un usage
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtUsage($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_usage = $this->getCtUsageById($_id);
                $this->deleteCtUsage($_usage);
            }
        }

        return true;
    }
}
