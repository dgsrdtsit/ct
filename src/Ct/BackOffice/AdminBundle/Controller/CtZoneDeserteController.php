<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtZoneDeserteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtZoneDeserte;

/**
 * Class CtZoneDeserteController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtZoneDeserteController extends Controller
{
    /**
     * Afficher tout les zone_desertes
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_zone_deserte_manager = $this->get(ServiceName::SRV_METIER_ZONE_DESERTE);

        // Récupérer tout les carosséries
        $_zone_desertes = $_zone_deserte_manager->getAllCtZoneDeserteByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtZoneDeserte:index.html.twig', array(
            'zone_desertes' => $_zone_desertes,
        ));
    }

    /**
     * Affichage page modification zone_deserte
     * @param CtZoneDeserte $_zone_deserte
     * @return Render page
     */
    public function editAction(CtZoneDeserte $_zone_deserte)
    {
        if (!$_zone_deserte) {
            throw $this->createNotFoundException('Unable to find zone_deserte entity.');
        }

        $_edit_form = $this->createEditForm($_zone_deserte);

        return $this->render('AdminBundle:CtZoneDeserte:edit.html.twig', array(
            'zone_deserte' => $_zone_deserte,
            'edit_form'    => $_edit_form->createView()
        ));
    }

    /**
     * Création zone_deserte
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_zone_deserte_manager = $this->get(ServiceName::SRV_METIER_ZONE_DESERTE);

        $_zone_deserte = new CtZoneDeserte();
        $_form         = $this->createCreateForm($_zone_deserte);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement carossérie
            $_zone_deserte_manager->saveCtZoneDeserte($_zone_deserte, 'new');

            $_zone_deserte_manager->setFlash('success', "Zone de déserte ajoutée");

            return $this->redirect($this->generateUrl('zone_deserte_index'));
        }

        return $this->render('AdminBundle:CtZoneDeserte:add.html.twig', array(
            'zone_deserte' => $_zone_deserte,
            'form'         => $_form->createView(),
        ));
    }

    /**
     * Modification zone_deserte
     * @param Request $_request requête
     * @param CtZoneDeserte $_zone_deserte
     * @return Render page
     */
    public function updateAction(Request $_request, CtZoneDeserte $_zone_deserte)
    {
        // Récupérer manager
        $_zone_deserte_manager = $this->get(ServiceName::SRV_METIER_ZONE_DESERTE);

        if (!$_zone_deserte) {
            throw $this->createNotFoundException('Unable to find zone_deserte entity.');
        }

        $_edit_form = $this->createEditForm($_zone_deserte);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_zone_deserte_manager->saveCtZoneDeserte($_zone_deserte, 'update');

            $_zone_deserte_manager->setFlash('success', "Zone de déserte modifiée");

            return $this->redirect($this->generateUrl('zone_deserte_index'));
        }

        return $this->render('AdminBundle:CtZoneDeserte:edit.html.twig', array(
            'zone_deserte' => $_zone_deserte,
            'edit_form'    => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition zone_deserte
     * @param CtZoneDeserte $_zone_deserte The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtZoneDeserte $_zone_deserte)
    {
        $_form = $this->createForm(CtZoneDeserteType::class, $_zone_deserte, array(
            'action' => $this->generateUrl('zone_deserte_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création zone_deserte
     * @param CtZoneDeserte $_zone_deserte The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtZoneDeserte $_zone_deserte)
    {
        $_form = $this->createForm(CtZoneDeserteType::class, $_zone_deserte, array(
            'action' => $this->generateUrl('zone_deserte_update', array('id' => $_zone_deserte->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression zone_deserte
     * @param Request $_request requête
     * @param CtZoneDeserte $_zone_deserte
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtZoneDeserte $_zone_deserte)
    {
        // Récupérer manager
        $_zone_deserte_manager = $this->get(ServiceName::SRV_METIER_ZONE_DESERTE);

        $_form = $this->createDeleteForm($_zone_deserte);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_zone_deserte_manager->deleteCtZoneDeserte($_zone_deserte);

            $_zone_deserte_manager->setFlash('success', 'Zone de déserte supprimée');
        }

        return $this->redirectToRoute('zone_deserte_index');
    }

    /**
     * Création formulaire de suppression zone_deserte
     * @param CtZoneDeserte $_zone_deserte The zone_deserte entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtZoneDeserte $_zone_deserte)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('zone_deserte_delete', array('id' => $_zone_deserte->getId())))
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
        $_zone_deserte_manager = $this->get(ServiceName::SRV_METIER_ZONE_DESERTE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_zone_deserte_manager->deleteGroupCtZoneDeserte($_ids);
        }

        $_zone_deserte_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('zone_deserte_index'));
    }
}
