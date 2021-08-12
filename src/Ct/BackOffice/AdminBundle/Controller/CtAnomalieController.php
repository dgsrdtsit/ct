<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtAnomalieType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtAnomalie;

/**
 * Class CtAnomalieController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtAnomalieController extends Controller
{
    /**
     * Afficher tout les anomalies
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_anomalie_manager = $this->get(ServiceName::SRV_METIER_ANOMALIE);

        // Récupérer tout les carosséries
        $_anomalies = $_anomalie_manager->getAllCtAnomalieByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtAnomalie:index.html.twig', array(
            'anomalies' => $_anomalies,
        ));
    }

    /**
     * Affichage page modification anomalie
     * @param CtAnomalie $_anomalie
     * @return Render page
     */
    public function editAction(CtAnomalie $_anomalie)
    {
        if (!$_anomalie) {
            throw $this->createNotFoundException('Unable to find anomalie entity.');
        }

        $_edit_form = $this->createEditForm($_anomalie);

        return $this->render('AdminBundle:CtAnomalie:edit.html.twig', array(
            'anomalie' => $_anomalie,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création anomalie
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_anomalie_manager = $this->get(ServiceName::SRV_METIER_ANOMALIE);

        $_anomalie = new CtAnomalie();
        $_form       = $this->createCreateForm($_anomalie);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement carossérie
            $_anomalie_manager->saveCtAnomalie($_anomalie, 'new');

            $_anomalie_manager->setFlash('success', "Anomalie ajoutée");

            return $this->redirect($this->generateUrl('anomalie_index'));
        }

        return $this->render('AdminBundle:CtAnomalie:add.html.twig', array(
            'anomalie' => $_anomalie,
            'form'       => $_form->createView(),
        ));
    }

    /**
     * Modification anomalie
     * @param Request $_request requête
     * @param CtAnomalie $_anomalie
     * @return Render page
     */
    public function updateAction(Request $_request, CtAnomalie $_anomalie)
    {
        // Récupérer manager
        $_anomalie_manager = $this->get(ServiceName::SRV_METIER_ANOMALIE);

        if (!$_anomalie) {
            throw $this->createNotFoundException('Unable to find anomalie entity.');
        }

        $_edit_form = $this->createEditForm($_anomalie);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_anomalie_manager->saveCtAnomalie($_anomalie, 'update');

            $_anomalie_manager->setFlash('success', "Anomalie modifiée");

            return $this->redirect($this->generateUrl('anomalie_index'));
        }

        return $this->render('AdminBundle:CtAnomalie:edit.html.twig', array(
            'anomalie' => $_anomalie,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition anomalie
     * @param CtAnomalie $_anomalie The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtAnomalie $_anomalie)
    {
        $_form = $this->createForm(CtAnomalieType::class, $_anomalie, array(
            'action' => $this->generateUrl('anomalie_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création anomalie
     * @param CtAnomalie $_anomalie The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtAnomalie $_anomalie)
    {
        $_form = $this->createForm(CtAnomalieType::class, $_anomalie, array(
            'action' => $this->generateUrl('anomalie_update', array('id' => $_anomalie->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression anomalie
     * @param Request $_request requête
     * @param CtAnomalie $_anomalie
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtAnomalie $_anomalie)
    {
        // Récupérer manager
        $_anomalie_manager = $this->get(ServiceName::SRV_METIER_ANOMALIE);

        $_form = $this->createDeleteForm($_anomalie);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_anomalie_manager->deleteCtAnomalie($_anomalie);

            $_anomalie_manager->setFlash('success', 'Anomalie supprimée');
        }

        return $this->redirectToRoute('anomalie_index');
    }

    /**
     * Création formulaire de suppression anomalie
     * @param CtAnomalie $_anomalie The anomalie entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtAnomalie $_anomalie)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('anomalie_delete', array('id' => $_anomalie->getId())))
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
        $_anomalie_manager = $this->get(ServiceName::SRV_METIER_ANOMALIE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_anomalie_manager->deleteGroupCtAnomalie($_ids);
        }

        $_anomalie_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('anomalie_index'));
    }
}
