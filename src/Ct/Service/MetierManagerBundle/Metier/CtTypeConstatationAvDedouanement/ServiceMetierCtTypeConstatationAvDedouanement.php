<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtTypeConstatationAvDedouanement;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtTypeConstatationAvDedouanement
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
     * Récuperer le repository constatation
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_TYPE_CONST_AV_DED);
    }

    /**
     * Récuperer tout les Anomalies
     * @return array
     */
    public function getAllCtTypeConstatationAvDedouanement()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tous les types de constatation avant dédouanement par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtTypeConstatationAvDedouanementByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un type constatation avant dédouanement par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtTypeConstatationAvDedouanementById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Récuperer un type constatation avant dédouanement par libelle
     * @param string $_libelle
     * @return array
     */
    public function getCtTypeConstatationAvDedouanementByLibelle($_libelle)
    {
        $_entity_m = EntityName::CT_TYPE_CONST_AV_DED;

        $_dql    = "SELECT t
                    FROM $_entity_m t 
                    WHERE t.cadTpLibelle LIKE ?1";
        $_query  = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter(1, '%'.$_libelle.'%');
        $_ret = $_query->getResult();
        if (count($_ret) > 0 && is_array($_ret))
            return $_ret[0];
        return $_ret;
    }

    /**
     * Enregistrer un type constatation avant dédouanement
     * @param Entity $_type_const_av_ded
     * @param string $_action
     * @return boolean
     */
    public function saveCtTypeConstatationAvDedouanement($_type_const_av_ded, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_type_const_av_ded);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un type constatation avant dédouanement
     * @param Entity $_type_const_av_ded
     * @return boolean
     */
    public function deleteCtTypeConstatationAvDedouanement($_type_const_av_ded)
    {
        $this->_entity_manager->remove($_type_const_av_ded);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un type constatation avant dédouanement
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtTypeConstatationAvDedouanement($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_type_const_av_ded = $this->getCtTypeConstatationAvDedouanementById($_id);
                $this->deleteCtTypeConstatationAvDedouanement($_type_const_av_ded);
            }
        }

        return true;
    }
}
