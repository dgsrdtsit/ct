<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtRoleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtRole;

/**
 * Class CtRoleController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtRoleController extends Controller
{
    /**
     * Afficher tout les rôles
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_role_manager = $this->get(ServiceName::SRV_METIER_ROLE);

        // Récupérer tout les role
        $_roles = $_role_manager->getAllCtRoleByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtRole:index.html.twig', array(
            'roles' => $_roles,
        ));
    }

    /**
     * Affichage page modification rôle
     * @param CtRole $_role
     * @return Render page
     */
    public function editAction(CtRole $_role)
    {
        if (!$_role) {
            throw $this->createNotFoundException('Unable to find CtRole entity.');
        }

        $_edit_form = $this->createEditForm($_role);

        return $this->render('AdminBundle:CtRole:edit.html.twig', array(
            'role'      => $_role,
            'edit_form' => $_edit_form->createView()
        ));
    }

    /**
     * Création rôle
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_role_manager = $this->get(ServiceName::SRV_METIER_ROLE);

        $_role = new CtRole();
        $_form = $this->createCreateForm($_role);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement rôle
            $_role_manager->saveCtRole($_role, 'new');

            $_role_manager->setFlash('success', "Rôle ajouté");

            return $this->redirect($this->generateUrl('role_index'));
        }

        return $this->render('AdminBundle:CtRole:add.html.twig', array(
            'role' => $_role,
            'form' => $_form->createView(),
        ));
    }

    /**
     * Modification rôle
     * @param Request $_request requête
     * @param CtRole $_role
     * @return Render page
     */
    public function updateAction(Request $_request, CtRole $_role)
    {
        // Récupérer manager
        $_role_manager = $this->get(ServiceName::SRV_METIER_ROLE);

        if (!$_role) {
            throw $this->createNotFoundException('Unable to find CtRole entity.');
        }

        $_edit_form = $this->createEditForm($_role);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_role_manager->saveCtRole($_role, 'update');

            $_role_manager->setFlash('success', "Rôle modifié");

            return $this->redirect($this->generateUrl('role_index'));
        }

        return $this->render('AdminBundle:CtRole:edit.html.twig', array(
            'role'      => $_role,
            'edit_form' => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition rôle
     * @param CtRole $_role The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtRole $_role)
    {
        $_form = $this->createForm(CtRoleType::class, $_role, array(
            'action' => $this->generateUrl('role_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création rôle
     * @param CtRole $_role The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtRole $_role)
    {
        $_form = $this->createForm(CtRoleType::class, $_role, array(
            'action' => $this->generateUrl('role_update', array('id' => $_role->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression rôle
     * @param Request $_request requête
     * @param CtRole $_role
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtRole $_role)
    {
        // Récupérer manager
        $_role_manager = $this->get(ServiceName::SRV_METIER_ROLE);

        $_form = $this->createDeleteForm($_role);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression rôle
            $_role_manager->deleteCtRole($_role);

            $_role_manager->setFlash('success', 'Rôle supprimé');
        }

        return $this->redirectToRoute('role_index');
    }

    /**
     * Création formulaire de suppression rôle
     * @param CtRole $_role The CtRole entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtRole $_role)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('role_delete', array('id' => $_role->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste rôle
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_role_manager = $this->get(ServiceName::SRV_METIER_ROLE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_role_manager->deleteGroupCtRole($_ids);
        }

        $_role_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('role_index'));
    }
}
