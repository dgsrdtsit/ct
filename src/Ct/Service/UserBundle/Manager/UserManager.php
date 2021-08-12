<?php

namespace Ct\Service\UserBundle\Manager;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Symfony\Component\DependencyInjection\Container;

class UserManager
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
     * Tester si User est connecte
     * @param User $_user
     * @return boolean
     */
    public function isUserConnected($_user) {
        $_user_connected    = $this->_container->get('security.token_storage')->getToken()->getUser();
        if ($_user->getId() === $_user_connected->getId()) return true;
        return false;
    }

    /**
     * Tester si CHEF_DE_CENTRE
     * @return integer
     */
    public function isChefDeCentre()
    {
        $_user_connected    = $this->_container->get('security.token_storage')->getToken()->getUser();
        $_role_user         = $_user_connected->getCtRole()->getId();
        if ($_role_user != RoleName::ID_ROLE_ADMIN) {
            return 0;
        }
        return $_user_connected->getCtCentre()->getId();
    }

    /**
     * Tester si SUPERADMIN
     * @return boolean
     */
    public function isSuperAdmin()
    {
        $_user_connected    = $this->_container->get('security.token_storage')->getToken()->getUser();
        $_role_user         = $_user_connected->getCtRole()->getId();
        if ($_role_user != RoleName::ID_ROLE_SUPERADMIN) {
            return false;
        }
        return true;
    }

    /**
     * Récuperer le repository utilisateur
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::USER);
    }

    /**
     * Récuperer tout les utilisateurs
     * @return array
     */
    public function getAllUser()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les utilisateurs d'un centre
     * @param int $_centre_id
     * @param array $_order
     * @return array
     */
    public function getAllUserByCentre($_centre_id, $_order)
    {
        $_array_type = array(
            'ctCentre'  => $_centre_id,
            'ctRole'    => array(
                RoleName::ID_ROLE_SUPERADMIN,
                RoleName::ID_ROLE_ADMIN,
                RoleName::ID_ROLE_RECEPTION,
                RoleName::ID_ROLE_VISITE,
                RoleName::ID_ROLE_STAFF,
                RoleName::ID_ROLE_APPRO
                )
        );
        if ($this->isChefDeCentre())
            $_array_type = array(
                'ctCentre'  => $_centre_id,
                'ctRole'    => array(
                    RoleName::ID_ROLE_ADMIN,
                    RoleName::ID_ROLE_RECEPTION,
                    RoleName::ID_ROLE_VISITE,
                    RoleName::ID_ROLE_STAFF,
                    RoleName::ID_ROLE_APPRO
                )
            );
        return $this->getRepository()->findBy($_array_type, $_order);
    }

    /**
     * Récuperer tout les utilisateurs par ordre
     * @param array $_order
     * @return array
     */
    public function getAllUserByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer tout les vérificateurs par rôle
     * @param array $_role
     * @param array $_order
     * @return array
     */
    public function getAllUserByRole($_role, $_order)
    {
        return $this->getRepository()->findBy($_role, $_order);
    }

    /**
     * Récuperer un utilisateur par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getUserById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Tester l'existence username
     * @param string $_username
     * @return boolean
     */
    public function isUsernameExist($_username) {
        $_exist = $this->getRepository()->findByUsername($_username);
        if ($_exist) {
            return true;
        }

        return false;
    }

    /**
     * Tester l'existence email
     * @param string $_email
     * @return boolean
     */
    public function isEmailExist($_email) {
        $_exist = $this->getRepository()->findByEmail($_email);
        if ($_exist) {
            return true;
        }

        return false;
    }

    /**
     * Ajouter un utilisateur
     * @param Entity $_user
     * @param array $_form
     * @return boolean
     */
    public function addUser($_user, $_form) {
        // Activer par défaut
        $_user->setEnabled(1);

        // Traitement rôle utilisateur
        $_role_manager = $this->_container->get(ServiceName::SRV_METIER_ROLE);
        $_role         = $_role_manager->getCtRoleById($_form['id_role']);
        $_rolename     = $_role->getRoleName();
        $_user_role    = 'ROLE_'.trim(strtoupper($_rolename));
        $_user->setCtRole($_role);
        $_user->setRoles(array($_user_role));

        $this->saveUser($_user, 'new');
    }


    /**
     * Ajouter un verificateur
     * @param Entity $_user
     * @param Entity $_user_connected
     * @return boolean
     */
    public function addVerificateur($_user, $_user_connected) {
        // Activer par défaut
        $_user->setEnabled(1);

        // Traitement rôle utilisateur
        $_role_manager = $this->_container->get(ServiceName::SRV_METIER_ROLE);
        $_role         = $_role_manager->getCtRoleById(RoleName::ID_ROLE_VERIFICATEUR);
        $_user_role    = RoleName::ROLE_VERIFICATEUR;
        $_user->setCtRole($_role);
        $_user->setRoles(array($_user_role));

        // Traitement centre
        if ($_user_connected->getCtRole()->getId() != RoleName::ID_ROLE_SUPERADMIN) {
            $_user_centre = $_user_connected->getCtCentre();
            $_user->setCtCentre($_user_centre);
        }

        // Traitement mot de passe
        $_user->setUsername(uniqid());
        $_user->setPlainPassword(md5(uniqid()));

        $this->saveUser($_user, 'new');
    }

    /**
     * Liste des verificateurs par centre
     * @param $_centre_id
     * @return array
     */
    public function getListVerificateurByCentre($_centre_id, $_is_present = -1)
    {
        if ($_centre_id > 0) {
            if ($_is_present >= 0) {
                return $this->getAllUserByRole(array(
                    'ctRole'    => RoleName::ID_ROLE_VERIFICATEUR,
                    'ctCentre'  => $_centre_id,
                    'usrPresence' => $_is_present),
                    array('id' => 'ASC')
                );
            } else {
                return $this->getAllUserByRole(array(
                    'ctRole'    => RoleName::ID_ROLE_VERIFICATEUR,
                    'ctCentre'  => $_centre_id),
                    array('id' => 'ASC')
                );
            }

        } else {
            if ($_is_present >= 0) {
                return $this->getAllUserByRole(array('ctRole' => RoleName::ID_ROLE_VERIFICATEUR, 'usrPresence' => $_is_present), array('id' => 'ASC'));
            } else {
                return $this->getAllUserByRole(array('ctRole' => RoleName::ID_ROLE_VERIFICATEUR), array('id' => 'ASC'));
            }

        }
    }

    /**
     * Retourner verificateur par Id
     * @param $_verif_id
     * @return array
     */
    public function getVerificateurById($_verif_id)
    {
        return $this->getAllUserByRole(array(
            'ctRole'    => RoleName::ID_ROLE_VERIFICATEUR,
            'id'        => $_verif_id),
            array('id' => 'ASC')
        );
    }

    /**
     * Modifier un utilisateur
     * @param Entity $_user
     * @param array $_form
     * @return boolean
     */
    public function updateUser($_user, $_form) {
        // Traitement rôle utilisateur
        $_role_manager = $this->_container->get(ServiceName::SRV_METIER_ROLE);
        $_role         = $_role_manager->getCtRoleById($_form['id_role']);
        $_rolename     = $_role->getRoleName();
        $_user_role    = 'ROLE_'.trim(strtoupper($_rolename));
        $_user->setCtRole($_role);
        $_user->setRoles(array($_user_role));

        // Date de modification
        $_user->setUsrUpdatedAt(new \DateTime());

        // Mise à jour mot de passe
        $_fos_user_manager = $this->_container->get('fos_user.user_manager');
        $_fos_user_manager->updatePassword($_user);

        $this->saveUser($_user, 'update');
    }

    /**
     * Modifier un utilisateur
     * @param Entity $_user
     * @param Entity $_user_connected
     * @return boolean
     */
    public function updateVerificateur($_user, $_user_connected) {
        // Traitement centre
        if ($_user_connected->getCtRole()->getId() != RoleName::ID_ROLE_SUPERADMIN) {
            $_user_centre = $_user_connected->getCtCentre();
            $_user->setCtCentre($_user_centre);
        }

        $this->saveUser($_user, 'update');
    }

    /**
     * Enregistrer un utilisateur
     * @param Entity $_user
     * @param string $_action
     * @return boolean
     */
    public function saveUser($_user, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_user);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un utilisateur
     * @param Entity $_user
     * @return boolean
     */
    public function deleteUser($_user)
    {
        $this->_entity_manager->remove($_user);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un utilisateur
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupUser($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                // Suppression utilisateur
                $_user = $this->getUserById($_id);
                $this->deleteUser($_user);
            }
        }

        return true;
    }

    /**
     * Récuperer tout les utilisateurs par type
     * @return array
     */
    public function getAllUserByType()
    {
        // Récupérer l'utilisateur connecté
        $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();
        $_id_user        = $_user_connected->getId();

        $_array_type = array(
            'ctRole' => array(
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
                'ctRole' => array(
                    RoleName::ID_ROLE_ADMIN,
                    RoleName::ID_ROLE_RECEPTION,
                    RoleName::ID_ROLE_VISITE,
                    RoleName::ID_ROLE_STAFF,
                    RoleName::ID_ROLE_APPRO
                )
            );

        return $this->getRepository()->findBy($_array_type, array('id' => 'DESC'));
    }
}
