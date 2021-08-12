<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtRole;

use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtRole
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
     * Récuperer le repository rôle
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_ROLE);
    }

    /**
     * Récuperer tout les rôles
     * @return array
     */
    public function getAllCtRole()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les rôle par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtRoleByOrder($_order)
    {

        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un rôle par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtRoleById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un rôle
     * @param Entity $_role
     * @param string $_action
     * @return boolean
     */
    public function saveCtRole($_role, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_role);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un rôle
     * @param Entity $_role
     * @return boolean
     */
    public function deleteCtRole($_role)
    {
        $this->_entity_manager->remove($_role);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un rôle
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtRole($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_role = $this->getCtRoleById($_id);
                $this->deleteCtRole($_role);
            }
        }

        return true;
    }

    /**
     * Récuperer tout les rôle par type
     * @return array
     */
    public function getAllCtRoleByType()
    {
        // Récupérer l'utilisateur connecté
        $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();
        $_id_user        = $_user_connected->getId();

        $_array_type = array(
            'id' => array(
                RoleName::ID_ROLE_SUPERADMIN,
                RoleName::ID_ROLE_ADMIN,
                RoleName::ID_ROLE_RECEPTION,
                RoleName::ID_ROLE_VISITE,
                RoleName::ID_ROLE_STAFF,
                RoleName::ID_ROLE_APPRO
            )
        );
        if ($_id_user == RoleName::ID_ROLE_ADMIN)
            $_array_type = array(
                'id' => array(
                    RoleName::ID_ROLE_ADMIN,
                    RoleName::ID_ROLE_RECEPTION,
                    RoleName::ID_ROLE_VISITE,
                    RoleName::ID_ROLE_STAFF,
                    RoleName::ID_ROLE_APPRO
                )
            );

        return $this->getRepository()->findBy($_array_type, array('roleName' => 'ASC'));
    }
}
