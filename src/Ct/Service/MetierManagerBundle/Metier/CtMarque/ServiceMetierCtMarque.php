<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtMarque;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtMarque
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
     * Récuperer le repository marque
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_MARQUE);
    }

    /**
     * Récuperer tout les marques
     * @return array
     */
    public function getAllCtMarque()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les marques par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtMarqueByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un marque par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtMarqueById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un marque
     * @param Entity $_marque
     * @param string $_action
     * @return boolean
     */
    public function saveCtMarque($_marque, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_marque);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un marque
     * @param Entity $_marque
     * @return boolean
     */
    public function deleteCtMarque($_marque)
    {
        $this->_entity_manager->remove($_marque);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un marque
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtMarque($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_marque = $this->getCtMarqueById($_id);
                $this->deleteCtMarque($_marque);
            }
        }

        return true;
    }
}
