<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtMarqueType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtMarque;

/**
 * Class CtMarqueController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtMarqueController extends Controller
{
    /**
     * Afficher tout les marques
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_marque_manager = $this->get(ServiceName::SRV_METIER_MARQUE);

        // Récupérer tout les carosséries
        $_marques = $_marque_manager->getAllCtMarqueByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtMarque:index.html.twig', array(
            'marques' => $_marques,
        ));
    }

    /**
     * Affichage page modification marque
     * @param CtMarque $_marque
     * @return Render page
     */
    public function editAction(CtMarque $_marque)
    {
        if (!$_marque) {
            throw $this->createNotFoundException('Unable to find marque entity.');
        }

        $_edit_form = $this->createEditForm($_marque);

        return $this->render('AdminBundle:CtMarque:edit.html.twig', array(
            'marque' => $_marque,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création marque
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_marque_manager = $this->get(ServiceName::SRV_METIER_MARQUE);

        $_marque = new CtMarque();
        $_form       = $this->createCreateForm($_marque);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement carossérie
            $_marque_manager->saveCtMarque($_marque, 'new');

            $_marque_manager->setFlash('success', "Marque ajoutée");

            return $this->redirect($this->generateUrl('marque_index'));
        }

        return $this->render('AdminBundle:CtMarque:add.html.twig', array(
            'marque' => $_marque,
            'form'       => $_form->createView(),
        ));
    }

    /**
     * Modification marque
     * @param Request $_request requête
     * @param CtMarque $_marque
     * @return Render page
     */
    public function updateAction(Request $_request, CtMarque $_marque)
    {
        // Récupérer manager
        $_marque_manager = $this->get(ServiceName::SRV_METIER_MARQUE);

        if (!$_marque) {
            throw $this->createNotFoundException('Unable to find marque entity.');
        }

        $_edit_form = $this->createEditForm($_marque);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_marque_manager->saveCtMarque($_marque, 'update');

            $_marque_manager->setFlash('success', "Marque modifiée");

            return $this->redirect($this->generateUrl('marque_index'));
        }

        return $this->render('AdminBundle:CtMarque:edit.html.twig', array(
            'marque' => $_marque,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition marque
     * @param CtMarque $_marque The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtMarque $_marque)
    {
        $_form = $this->createForm(CtMarqueType::class, $_marque, array(
            'action' => $this->generateUrl('marque_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création marque
     * @param CtMarque $_marque The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtMarque $_marque)
    {
        $_form = $this->createForm(CtMarqueType::class, $_marque, array(
            'action' => $this->generateUrl('marque_update', array('id' => $_marque->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression marque
     * @param Request $_request requête
     * @param CtMarque $_marque
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtMarque $_marque)
    {
        // Récupérer manager
        $_marque_manager = $this->get(ServiceName::SRV_METIER_MARQUE);

        $_form = $this->createDeleteForm($_marque);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_marque_manager->deleteCtMarque($_marque);

            $_marque_manager->setFlash('success', 'Marque supprimée');
        }

        return $this->redirectToRoute('marque_index');
    }

    /**
     * Création formulaire de suppression marque
     * @param CtMarque $_marque The marque entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtMarque $_marque)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marque_delete', array('id' => $_marque->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste carossérie
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_marque_manager = $this->get(ServiceName::SRV_METIER_MARQUE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_marque_manager->deleteGroupCtMarque($_ids);
        }

        $_marque_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('marque_index'));
    }
}
