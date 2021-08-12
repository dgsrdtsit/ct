<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtImprimeTech;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtImprimeTech
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
        return $this->_entity_manager->getRepository(EntityName::CT_IMPRIME_TECH);
    }

    /**
     * Récuperer tout les imprimés tech
     * @return array
     */
    public function getAllCtImprimeTech()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les imprimés tech par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtImprimeTechByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un imprimé tech par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtImprimeTechById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un imprimé tech
     * @param Entity $_imprime_tech
     * @param string $_action
     * @return boolean
     */
    public function saveCtImprimeTech($_imprime_tech, $_action)
    {
        // Récuperer ID utilisateur
        $ct_user_id = $this->_container->get('security.token_storage')->getToken()->getUser();
        $_imprime_tech->setCtUser($ct_user_id);
        // Date de modification
        $_imprime_tech->setPrttUpdatedAt(new \DateTime());

        if ($_action == 'new') {
            // Date de création
            $_imprime_tech->setPrttCreatedAt(new \DateTime());
            // Date de modification
            $_imprime_tech->setPrttUpdatedAt(NULL);
            $this->_entity_manager->persist($_imprime_tech);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un imprimé tech
     * @param Entity $_imprime_tech
     * @return boolean
     */
    public function deleteCtImprimeTech($_imprime_tech)
    {
        $this->_entity_manager->remove($_imprime_tech);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un imprimé tech
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtImprimeTech($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_imprime_tech = $this->getCtImprimeTechById($_id);
                $this->deleteCtImprimeTech($_imprime_tech);
            }
        }

        return true;
    }

    /**
     *  Récupérer un imprimé tech par son nom
     *  @param  $_nom_imprime_tech  : Nom imprimé tech
     *  @return $_imprime_tech      : Liste imprimés tech array()
     */
    public function getCtImprimeTechByNomImprimeTech($_nom_imprime_tech)
    {
        $_entity_i = EntityName::CT_IMPRIME_TECH;


        $_dql    = "SELECT t
                    FROM $_entity_i t 
                    WHERE t.nomImprimeTech LIKE ?1";
        $_query  = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter(1, '%'.$_nom_imprime_tech.'%');
        $_ret = $_query->getResult();
        if (count($_ret) > 0 && is_array($_ret))
            return $_ret[0];
        return $_ret;
    }
}
