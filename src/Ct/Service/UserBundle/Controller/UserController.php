<?php

namespace Ct\Service\UserBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ct\Service\UserBundle\Entity\User;
use Ct\Service\UserBundle\Form\UserType;

/**
 * Class UserController
 *
 * @package UserBundle\Controller
 */
class UserController extends Controller
{
    /**
     * Afficher tout les utilisateurs
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_user_manager = $this->get(ServiceName::SRV_METIER_USER);

        $_is_super_admin = $_user_manager->isSuperAdmin();
        $_centre_id = $_user_manager->isChefDeCentre();

        if (!$_is_super_admin && !$_centre_id) { // Not SUPER_ADMIN and Not ADMIN
            $_show_index_page = false;
        } else { // SUPER_ADMIN and ADMIN can see index
            $_show_index_page = true;
        }

        if ($_show_index_page === false) {
            throw $this->createAccessDeniedException('You cannot access this page!');
        }

        // Récupérer tout les utilisateurs
        if ($_centre_id > 0) {
            $_users = $_user_manager->getAllUserByCentre($_centre_id, array('id' => 'DESC'));
        } else {
            $_users = $_user_manager->getAllUserByType();
        }
        return $this->render('UserBundle:User:index.html.twig', array(
            'users' => $_users,
        ));
    }

    /**
     * Affichage page modification utilisateur
     * @param User $_user
     * @return Render page
     */
    public function editAction(User $_user)
    {
        // Récupérer manager
        $_role_manager = $this->get(ServiceName::SRV_METIER_ROLE);
        $_user_manager = $this->get(ServiceName::SRV_METIER_USER);

        if (!$_user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $_is_super_admin = $_user_manager->isSuperAdmin();
        $_centre_id = $_user_manager->isChefDeCentre();
        $_is_same_user_as_connected = $_user_manager->isUserConnected($_user);

        if (!$_is_super_admin && !$_centre_id && !$_is_same_user_as_connected) { // Not SUPER_ADMIN and Not ADMIN and Not same user connected
            $_show_edit_page = false;
        } elseif ($_centre_id) {
            if ($_centre_id !== $_user->getCtCentre()->getId()) { // ADMIN cannot edit User from another Centre
                $_show_edit_page = false;
            } else {
                $_show_edit_page = true;
            }
        } else { // SUPER_ADMIN can do all edit
            $_show_edit_page = true;
        }

        if ($_show_edit_page === false) {
            throw $this->createAccessDeniedException('You cannot access this page!');
        }

        $_edit_form = $this->createEditForm($_user);

        // Récupérer tout les rôles utilisateurs
        $_user_roles = $_role_manager->getAllCtRoleByType();

        return $this->render('UserBundle:User:edit.html.twig', array(
            'user'       => $_user,
            'edit_form'  => $_edit_form->createView(),
            'user_roles' => $_user_roles
        ));
    }

    /**
     * Création utilisateur
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_user_manager = $this->get(ServiceName::SRV_METIER_USER);
        $_role_manager = $this->get(ServiceName::SRV_METIER_ROLE);

        $_user = new User();
        $_form = $this->createCreateForm($_user);
        $_form->handleRequest($_request);

        // Récupérer tout les rôles utilisateurs
        $_user_roles = $_role_manager->getAllCtRoleByType();

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Récupération rôle
            $_data_forms       = $_request->request->all();
            $_forms            = array();
            $_forms['id_role'] = $_data_forms['ct_userbundle_user_role'];

            // Test existence email
//            $_email_exist = $_user_manager->isEmailExist($_form['email']->getData());
//            if ($_email_exist) {
//                $_user_manager->setFlash('error', "Email déjà pris");
//
//                return $this->redirectToRoute('user_new');
//            }
//
//            // Teste existence username
//            $_username_exist = $_user_manager->isUsernameExist($_form['username']->getData());
//            if ($_username_exist) {
//                $_user_manager->setFlash('error', "Nom d'utilisateur déjà pris");
//
//                return $this->redirectToRoute('user_new');
//            }

            // Enregistrement utilisateur
            $_user_manager->addUser($_user, $_forms);

            $_user_manager->setFlash('success', "Utilisateur ajouté");

            return $this->redirect($this->generateUrl('user_index'));
        }

        return $this->render('UserBundle:User:add.html.twig', array(
            'user'       => $_user,
            'form'       => $_form->createView(),
            'user_roles' => $_user_roles,
        ));
    }

    /**
     * Modification utilisateur
     * @param Request $_request requête
     * @param User $_user
     * @return Render page
     */
    public function updateAction(Request $_request, User $_user)
    {
        // Récupérer manager
        $_user_manager = $this->get(ServiceName::SRV_METIER_USER);

        if (!$_user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $_edit_form = $this->createEditForm($_user);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            // Récupération rôle
            $_data_forms       = $_request->request->all();
            $_forms            = array();
            $_forms['id_role'] = $_data_forms['ct_userbundle_user_role'];

            // Mise à jour utilisateur
            $_user_manager->updateUser($_user, $_forms);

            // Si le rôle est vérificateur, on rédirige vers la page liste vérificateur
            if ($_forms['id_role'] == RoleName::ID_ROLE_VERIFICATEUR) {
                $_user_manager->setFlash('success', "Vérificateur modifié");

                return $this->redirectToRoute('verificateur_index');
            }

            $_user_manager->setFlash('success', "Utilisateur modifié");

            return $this->redirectToRoute('user_edit', array('id' => $_user->getId()));
        }

        return $this->render('UserBundle:User:edit.html.twig', array(
            'user'       => $_user,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
 * Création formulaire d'édition utilisateur
 * @param User $_user The entity
 * @return \Symfony\Component\Form\Form The form
 */
    private function createCreateForm(User $_user)
    {
        $_form = $this->createForm(UserType::class, $_user, array(
            'action' => $this->generateUrl('user_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création utilisateur
     * @param User $_user The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $_user)
    {
        $_form = $this->createForm(UserType::class, $_user, array(
            'action' => $this->generateUrl('user_update', array('id' => $_user->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression utilisateur
     * @param Request $_request requête
     * @param User $_user
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, User $_user)
    {
        // Récupérer manager
        $_user_manager = $this->get(ServiceName::SRV_METIER_USER);

        $_form = $this->createDeleteForm($_user);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression utilisateur
            $_user_manager->deleteUser($_user);

            $_user_manager->setFlash('success', 'Utilisateur supprimé');
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Création formulaire de suppression utilisateur
     * @param User $_user The user entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $_user)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('user_delete', array('id' => $_user->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Ajax suppression fichier image utilisateur
     * @param Request $_request
     * @return JsonResponse
     */
    public function deleteImageAjaxAction(Request $_request) {
        // Récupérer manager
        $_user_upload_manager = $this->get(ServiceName::SRV_METIER_USER_UPLOAD);

        // Récuperation identifiant fichier
        $_data = $_request->request->all();
        $_id   = $_data['id'];

        // Suppression fichier image
        $_response = $_user_upload_manager->deleteImageById($_id);

        return new JsonResponse($_response);
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste utilisateur
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_user_manager = $this->get(ServiceName::SRV_METIER_USER);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_user_manager->deleteGroupUser($_ids);
        }

        $_user_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('user_index'));
    }

}
