<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtUtilisation;

use Ct\Service\MetierManagerBundle\Entity\CtUtilisation;
use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtUtilisation
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
     * Récuperer le repository Utilisation
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_UTILISATION);
    }

    /**
     * Récuperer tout les Utilisations
     * @return array
     */
    public function getAllCtUtilisation()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les utilisations par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtUtilisationByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer utilisation par libelle
     * @param string $_libelle
     * @return CtUtilisation
     */
    public function getCtUtilisationByLibelle($_libelle)
    {
        $_entity_u = EntityName::CT_UTILISATION;


        $_dql    = "SELECT u
                    FROM $_entity_u u 
                    WHERE u.utLibelle LIKE ?1";
        $_query  = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter(1, '%'.$_libelle.'%');
        $_ret = $_query->getResult();
        if (count($_ret) > 0 && is_array($_ret))
            return $_ret[0];
        return $_ret;
    }

    /**
     * Récuperer une utilisation par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtUtilisationById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un Utilisation
     * @param Entity $_utilisation
     * @param string $_action
     * @return boolean
     */
    public function saveCtUtilisation($_utilisation, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_utilisation);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un Utilisation
     * @param Entity $_utilisation
     * @return boolean
     */
    public function deleteCtUtilisation($_utilisation)
    {
        $this->_entity_manager->remove($_utilisation);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un Utilisation
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtUtilisation($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_utilisation = $this->getCtUtilisationById($_id);
                $this->deleteCtUtilisation($_utilisation);
            }
        }

        return true;
    }
}
