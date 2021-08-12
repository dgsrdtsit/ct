<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtUtilisationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtUtilisation;

/**
 * Class CtUtilisationController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtUtilisationController extends Controller
{
    /**
     * Afficher tout les utilisations
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_utilisation_manager = $this->get(ServiceName::SRV_METIER_UTILISATION);

        // Récupérer toutes les utilisations
        $_utilisations = $_utilisation_manager->getAllCtUtilisationByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtUtilisation:index.html.twig', array(
            'utilisations' => $_utilisations,
        ));
    }

    /**
     * Affichage page modification utilisation
     * @param CtUtilisation $_utilisation
     * @return Render page
     */
    public function editAction(CtUtilisation $_utilisation)
    {
        if (!$_utilisation) {
            throw $this->createNotFoundException('Unable to find utilisation entity.');
        }

        $_edit_form = $this->createEditForm($_utilisation);

        return $this->render('AdminBundle:CtUtilisation:edit.html.twig', array(
            'utilisation' => $_utilisation,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création utilisation
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_utilisation_manager = $this->get(ServiceName::SRV_METIER_UTILISATION);

        $_utilisation = new CtUtilisation();
        $_form       = $this->createCreateForm($_utilisation);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement carossérie
            $_utilisation_manager->saveCtUtilisation($_utilisation, 'new');

            $_utilisation_manager->setFlash('success', "utilisation ajoutée");

            return $this->redirect($this->generateUrl('utilisation_index'));
        }

        return $this->render('AdminBundle:CtUtilisation:add.html.twig', array(
            'utilisation' => $_utilisation,
            'form'       => $_form->createView(),
        ));
    }

    /**
     * Modification utilisation
     * @param Request $_request requête
     * @param CtUtilisation $_utilisation
     * @return Render page
     */
    public function updateAction(Request $_request, CtUtilisation $_utilisation)
    {
        // Récupérer manager
        $_utilisation_manager = $this->get(ServiceName::SRV_METIER_UTILISATION);

        if (!$_utilisation) {
            throw $this->createNotFoundException('Unable to find utilisation entity.');
        }

        $_edit_form = $this->createEditForm($_utilisation);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_utilisation_manager->saveCtUtilisation($_utilisation, 'update');

            $_utilisation_manager->setFlash('success', "utilisation modifiée");

            return $this->redirect($this->generateUrl('utilisation_index'));
        }

        return $this->render('AdminBundle:CtUtilisation:edit.html.twig', array(
            'utilisation' => $_utilisation,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition utilisation
     * @param CtUtilisation $_utilisation The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtUtilisation $_utilisation)
    {
        $_form = $this->createForm(CtUtilisationType::class, $_utilisation, array(
            'action' => $this->generateUrl('utilisation_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création utilisation
     * @param CtUtilisation $_utilisation The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtUtilisation $_utilisation)
    {
        $_form = $this->createForm(CtUtilisationType::class, $_utilisation, array(
            'action' => $this->generateUrl('utilisation_update', array('id' => $_utilisation->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression utilisation
     * @param Request $_request requête
     * @param CtUtilisation $_utilisation
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtUtilisation $_utilisation)
    {
        // Récupérer manager
        $_utilisation_manager = $this->get(ServiceName::SRV_METIER_UTILISATION);

        $_form = $this->createDeleteForm($_utilisation);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_utilisation_manager->deleteCtUtilisation($_utilisation);

            $_utilisation_manager->setFlash('success', 'utilisation supprimée');
        }

        return $this->redirectToRoute('utilisation_index');
    }

    /**
     * Création formulaire de suppression utilisation
     * @param CtUtilisation $_utilisation The utilisation entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtUtilisation $_utilisation)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('utilisation_delete', array('id' => $_utilisation->getId())))
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
        $_utilisation_manager = $this->get(ServiceName::SRV_METIER_UTILISATION);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_utilisation_manager->deleteGroupCtUtilisation($_ids);
        }

        $_utilisation_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('utilisation_index'));
    }
}
