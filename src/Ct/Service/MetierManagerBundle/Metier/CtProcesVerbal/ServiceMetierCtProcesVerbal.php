<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtProcesVerbal;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtProcesVerbal
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
     * Récuperer le repository proces_verbal
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_PROCES_VERBAL);
    }

    /**
     * Récuperer tout les proces_verbals
     * @return array
     */
    public function getAllCtProcesVerbal()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les proces_verbals par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtProcesVerbalByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un proces_verbal par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtProcesVerbalById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    public function getCtProcesVerbalByType($_str_pv_type)
    {
        $_pv = $this->getRepository()->findBy(array('pvType' => $_str_pv_type));
        return $_pv[0]->getPvTarif();
    }

    /**
     * Enregistrer un proces_verbal
     * @param Entity $_proces_verbal
     * @param string $_action
     * @return boolean
     */
    public function saveCtProcesVerbal($_proces_verbal, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_proces_verbal);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un proces_verbal
     * @param Entity $_proces_verbal
     * @return boolean
     */
    public function deleteCtProcesVerbal($_proces_verbal)
    {
        $this->_entity_manager->remove($_proces_verbal);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un proces_verbal
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtProcesVerbal($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_proces_verbal = $this->getCtProcesVerbalById($_id);
                $this->deleteCtProcesVerbal($_proces_verbal);
            }
        }

        return true;
    }
}
