<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtAnomalieType;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtTypeAnomalieType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtTypeAnomalie;

/**
 * Class CtTypeAnomalieController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtTypeAnomalieController extends Controller
{
    /**
     * Afficher tout les anomalies
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_type_anomalie_manager = $this->get(ServiceName::SRV_METIER_TYPE_ANOMALIE);

        // Récupérer tout les carosséries
        $_type_anomalies = $_type_anomalie_manager->getAllCtTypeAnomalieByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtTypeAnomalie:index.html.twig', array(
            'type_anomalies' => $_type_anomalies,
        ));
    }

    /**
     * Affichage page modification anomalie
     * @param CtAnomalieType $_type_anomalie
     * @return Render page
     */
    public function editAction(CtAnomalieType $_type_anomalie)
    {
        if (!$_type_anomalie) {
            throw $this->createNotFoundException('Unable to find Type anomalie entity.');
        }

        $_edit_form = $this->createEditForm($_type_anomalie);

        return $this->render('AdminBundle:CtTypeAnomalie:edit.html.twig', array(
            'type_anomalie' => $_type_anomalie,
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
        $_type_anomalie_manager = $this->get(ServiceName::SRV_METIER_TYPE_ANOMALIE);

        $_type_anomalie = new CtAnomalieType();
        $_form       = $this->createCreateForm($_type_anomalie);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement carossérie
            $_type_anomalie_manager->saveCtTypeAnomalie($_type_anomalie, 'new');

            $_type_anomalie_manager->setFlash('success', "Type anomalie ajouté");

            return $this->redirect($this->generateUrl('type_anomalie_index'));
        }

        return $this->render('AdminBundle:CtTypeAnomalie:add.html.twig', array(
            'type_anomalie' => $_type_anomalie,
            'form'       => $_form->createView(),
        ));
    }

    /**
     * Modification anomalie
     * @param Request $_request requête
     * @param CtAnomalieType $_type_anomalie
     * @return Render page
     */
    public function updateAction(Request $_request, CtAnomalieType $_type_anomalie)
    {
        // Récupérer manager
        $_type_anomalie_manager = $this->get(ServiceName::SRV_METIER_TYPE_ANOMALIE);

        if (!$_type_anomalie) {
            throw $this->createNotFoundException('Unable to find anomalie entity.');
        }

        $_edit_form = $this->createEditForm($_type_anomalie);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_type_anomalie_manager->saveCtTypeAnomalie($_type_anomalie, 'update');

            $_type_anomalie_manager->setFlash('success', "Type anomalie modifié");

            return $this->redirect($this->generateUrl('type_anomalie_index'));
        }

        return $this->render('AdminBundle:CtTypeAnomalie:edit.html.twig', array(
            'type_anomalie' => $_type_anomalie,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition anomalie
     * @param CtAnomalieType $_type_anomalie The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtAnomalieType $_type_anomalie)
    {
        $_form = $this->createForm(CtTypeAnomalieType::class, $_type_anomalie, array(
            'action' => $this->generateUrl('type_anomalie_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création anomalie
     * @param CtAnomalieType $_type_anomalie The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtAnomalieType $_type_anomalie)
    {
        $_form = $this->createForm(CtTypeAnomalieType::class, $_type_anomalie, array(
            'action' => $this->generateUrl('type_anomalie_update', array('id' => $_type_anomalie->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression anomalie
     * @param Request $_request requête
     * @param CtAnomalieType $_type_anomalie
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtAnomalieType $_type_anomalie)
    {
        // Récupérer manager
        $_type_anomalie_manager = $this->get(ServiceName::SRV_METIER_TYPE_ANOMALIE);

        $_form = $this->createDeleteForm($_type_anomalie);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_type_anomalie_manager->deleteCtTypeAnomalie($_type_anomalie);

            $_type_anomalie_manager->setFlash('success', 'Type anomalie supprimé');
        }

        return $this->redirectToRoute('type_anomalie_index');
    }

    /**
     * Création formulaire de suppression anomalie
     * @param CtAnomalieType $_type_anomalie The anomalie entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtAnomalieType $_type_anomalie)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('type_anomalie_delete', array('id' => $_type_anomalie->getId())))
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
        $_type_anomalie_manager = $this->get(ServiceName::SRV_METIER_TYPE_ANOMALIE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_type_anomalie_manager->deleteGroupCtTypeAnomalie($_ids);
        }

        $_type_anomalie_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('type_anomalie_index'));
    }
}
