<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtTypeReception;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtTypeReceptionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class CtTypeReceptionController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtTypeReceptionController extends Controller
{
    /**
     * Afficher tout les receptions
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_type_reception_manager = $this->get(ServiceName::SRV_METIER_TYPE_RECEPTION);

        // Récupérer tout les receptions types
        $_type_receptions = $_type_reception_manager->getAllCtTypeReceptionByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtTypeReception:index.html.twig', array(
            'type_receptions' => $_type_receptions,
        ));
    }

    /**
     * Affichage page modification reception
     * @param CtTypeReception $_type_reception
     * @return Render page
     */
    public function editAction(CtTypeReception $_type_reception)
    {
        if (!$_type_reception) {
            throw $this->createNotFoundException('Unable to find type reception entity.');
        }

        $_edit_form = $this->createEditForm($_type_reception);

        return $this->render('AdminBundle:CtTypeReception:edit.html.twig', array(
            'type_reception' => $_type_reception,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création reception
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_type_reception_manager = $this->get(ServiceName::SRV_METIER_TYPE_RECEPTION);

        $_type_reception = new CtTypeReception();
        $_form       = $this->createCreateForm($_type_reception);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {

            $_type_reception_manager->saveCtTypeReception($_type_reception, 'new');

            $_type_reception_manager->setFlash('success', "Type réception ajouté");

            return $this->redirect($this->generateUrl('type_reception_index'));
        }

        return $this->render('AdminBundle:CtTypeReception:add.html.twig', array(
            'type_reception' => $_type_reception,
            'form'       => $_form->createView(),
        ));
    }

    /**
     * Modification reception
     * @param Request $_request requête
     * @param CtTypeReception $_type_reception
     * @return Render page
     */
    public function updateAction(Request $_request, CtTypeReception $_type_reception)
    {
        // Récupérer manager
        $_type_reception_manager = $this->get(ServiceName::SRV_METIER_TYPE_RECEPTION);

        if (!$_type_reception) {
            throw $this->createNotFoundException('Unable to find reception entity.');
        }

        $_edit_form = $this->createEditForm($_type_reception);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_type_reception_manager->saveCtTypeReception($_type_reception, 'update');

            $_type_reception_manager->setFlash('success', "Type réception modifié");

            return $this->redirect($this->generateUrl('type_reception_index'));
        }

        return $this->render('AdminBundle:CtTypeReception:edit.html.twig', array(
            'type_reception' => $_type_reception,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition reception
     * @param CtTypeReception $_type_reception The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtTypeReception $_type_reception)
    {
        $_form = $this->createForm(CtTypeReceptionType::class, $_type_reception, array(
            'action' => $this->generateUrl('type_reception_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création reception
     * @param CtTypeReception $_type_reception The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtTypeReception $_type_reception)
    {
        $_form = $this->createForm(CtTypeReceptionType::class, $_type_reception, array(
            'action' => $this->generateUrl('type_reception_update', array('id' => $_type_reception->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression reception
     * @param Request $_request requête
     * @param CtTypeReception $_type_reception
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtTypeReception $_type_reception)
    {
        // Récupérer manager
        $_type_reception_manager = $this->get(ServiceName::SRV_METIER_TYPE_RECEPTION);

        $_form = $this->createDeleteForm($_type_reception);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_type_reception_manager->deleteCtTypeReception($_type_reception);

            $_type_reception_manager->setFlash('success', 'Type réception supprimé');
        }

        return $this->redirectToRoute('type_reception_index');
    }

    /**
     * Création formulaire de suppression reception
     * @param CtTypeReception $_type_reception The reception entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtTypeReception $_type_reception)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('type_reception_delete', array('id' => $_type_reception->getId())))
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
        $_type_reception_manager = $this->get(ServiceName::SRV_METIER_TYPE_RECEPTION);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_type_reception_manager->deleteGroupCtTypeReception($_ids);
        }

        $_type_reception_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('type_reception_index'));
    }
}
