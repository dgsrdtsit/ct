<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtTypeReception;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtTypeReception
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
     * Récuperer le repository Reception
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_TYPE_RECEPTION);
    }

    /**
     * Récuperer tout les Receptions
     * @return array
     */
    public function getAllCtTypeReception()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les receptions par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtTypeReceptionByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer une reception par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtTypeReceptionById($_id)
    {
        return $this->getRepository()->find($_id);
    }


    public function getTypeReception($_type_reception)
    {
        $_entity_t_r = EntityName::CT_TYPE_RECEPTION;


        $_dql    = "SELECT t
                    FROM $_entity_t_r t 
                    WHERE t.tprcpLibelle LIKE ?1";
        $_query  = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter(1, '%'.$_type_reception.'%');
        $_ret = $_query->getResult();
        if (count($_ret) > 0 && is_array($_ret))
            return $_ret[0];
        return $_ret;
    }

    /**
     * Enregistrer une Reception
     * @param Entity $_type_reception
     * @param string $_action
     * @return boolean
     */
    public function saveCtTypeReception($_type_reception, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_type_reception);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer une Reception
     * @param Entity $_type_reception
     * @return boolean
     */
    public function deleteCtTypeReception($_type_reception)
    {
        $this->_entity_manager->remove($_type_reception);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un Reception
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtTypeReception($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_reception = $this->getCtTypeReceptionById($_id);
                $this->deleteCtTypeReception($_reception);
            }
        }

        return true;
    }
}
