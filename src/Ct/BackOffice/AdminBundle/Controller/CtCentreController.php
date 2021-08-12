<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtCentreType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtCentre;

/**
 * Class CtCentreController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtCentreController extends Controller
{
    /**
     * Afficher tout les centre
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_centre_manager = $this->get(ServiceName::SRV_METIER_CENTRE);

        // Récupérer tout les centre
        $_centres = $_centre_manager->getAllCtCentreByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtCentre:index.html.twig', array(
            'centres' => $_centres,
        ));
    }

    /**
     * Affichage page modification centre
     * @param CtCentre $_centre
     * @return Render page
     */
    public function editAction(CtCentre $_centre)
    {
        if (!$_centre) {
            throw $this->createNotFoundException('Unable to find CtCentre entity.');
        }

        $_edit_form = $this->createEditForm($_centre);

        return $this->render('AdminBundle:CtCentre:edit.html.twig', array(
            'centre'    => $_centre,
            'edit_form' => $_edit_form->createView()
        ));
    }

    /**
     * Création centre
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_centre_manager = $this->get(ServiceName::SRV_METIER_CENTRE);

        $_centre = new CtCentre();
        $_form   = $this->createCreateForm($_centre);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement centre
            $_centre_manager->saveCtCentre($_centre, 'new');

            $_centre_manager->setFlash('success', "Centre ajouté");

            return $this->redirect($this->generateUrl('centre_index'));
        }

        return $this->render('AdminBundle:CtCentre:add.html.twig', array(
            'centre' => $_centre,
            'form'   => $_form->createView(),
        ));
    }

    /**
     * Modification centre
     * @param Request $_request requête
     * @param CtCentre $_centre
     * @return Render page
     */
    public function updateAction(Request $_request, CtCentre $_centre)
    {
        // Récupérer manager
        $_centre_manager = $this->get(ServiceName::SRV_METIER_CENTRE);

        if (!$_centre) {
            throw $this->createNotFoundException('Unable to find CtCentre entity.');
        }

        $_edit_form = $this->createEditForm($_centre);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_centre_manager->updateCtCentre($_centre);

            $_centre_manager->setFlash('success', "Centre modifié");

            return $this->redirect($this->generateUrl('centre_index'));
        }

        return $this->render('AdminBundle:CtCentre:edit.html.twig', array(
            'centre'    => $_centre,
            'edit_form' => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition centre
     * @param CtCentre $_centre The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtCentre $_centre)
    {
        $_form = $this->createForm(CtCentreType::class, $_centre, array(
            'action' => $this->generateUrl('centre_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création centre
     * @param CtCentre $_centre The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtCentre $_centre)
    {
        $_form = $this->createForm(CtCentreType::class, $_centre, array(
            'action' => $this->generateUrl('centre_update', array('id' => $_centre->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression centre
     * @param Request $_request requête
     * @param CtCentre $_centre
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtCentre $_centre)
    {
        // Récupérer manager
        $_centre_manager = $this->get(ServiceName::SRV_METIER_CENTRE);

        $_form = $this->createDeleteForm($_centre);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression centre
            $_centre_manager->deleteCtCentre($_centre);

            $_centre_manager->setFlash('success', 'Centre supprimé');
        }

        return $this->redirectToRoute('centre_index');
    }

    /**
     * Création formulaire de suppression centre
     * @param CtCentre $_centre The CtCentre entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtCentre $_centre)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('centre_delete', array('id' => $_centre->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste centre
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_centre_manager = $this->get(ServiceName::SRV_METIER_CENTRE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_centre_manager->deleteGroupCtCentre($_ids);
        }

        $_centre_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('centre_index'));
    }
}
