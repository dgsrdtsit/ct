<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtCarosserieType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtCarosserie;

/**
 * Class CtCarosserieController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtCarosserieController extends Controller
{
    /**
     * Afficher tout les carosseries
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_carosserie_manager = $this->get(ServiceName::SRV_METIER_CAROSSERIE);

        // Récupérer tout les carosséries
        $_carosseries = $_carosserie_manager->getAllCtCarosserieByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtCarosserie:index.html.twig', array(
            'carosseries' => $_carosseries,
        ));
    }

    /**
     * Affichage page modification carosserie
     * @param CtCarosserie $_carosserie
     * @return Render page
     */
    public function editAction(CtCarosserie $_carosserie)
    {
        if (!$_carosserie) {
            throw $this->createNotFoundException('Unable to find Carosserie entity.');
        }

        $_edit_form = $this->createEditForm($_carosserie);

        return $this->render('AdminBundle:CtCarosserie:edit.html.twig', array(
            'carosserie' => $_carosserie,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création carosserie
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_carosserie_manager = $this->get(ServiceName::SRV_METIER_CAROSSERIE);

        $_carosserie = new CtCarosserie();
        $_form       = $this->createCreateForm($_carosserie);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement carossérie
            $_carosserie_manager->saveCtCarosserie($_carosserie, 'new');

            $_carosserie_manager->setFlash('success', "Carrosserie ajoutée");

            return $this->redirect($this->generateUrl('carosserie_index'));
        }

        return $this->render('AdminBundle:CtCarosserie:add.html.twig', array(
            'carosserie' => $_carosserie,
            'form'       => $_form->createView(),
        ));
    }

    /**
     * Modification carosserie
     * @param Request $_request requête
     * @param CtCarosserie $_carosserie
     * @return Render page
     */
    public function updateAction(Request $_request, CtCarosserie $_carosserie)
    {
        // Récupérer manager
        $_carosserie_manager = $this->get(ServiceName::SRV_METIER_CAROSSERIE);

        if (!$_carosserie) {
            throw $this->createNotFoundException('Unable to find Carosserie entity.');
        }

        $_edit_form = $this->createEditForm($_carosserie);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_carosserie_manager->saveCtCarosserie($_carosserie, 'update');

            $_carosserie_manager->setFlash('success', "Carrosserie modifiée");

            return $this->redirect($this->generateUrl('carosserie_index'));
        }

        return $this->render('AdminBundle:CtCarosserie:edit.html.twig', array(
            'carosserie' => $_carosserie,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition carosserie
     * @param CtCarosserie $_carosserie The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtCarosserie $_carosserie)
    {
        $_form = $this->createForm(CtCarosserieType::class, $_carosserie, array(
            'action' => $this->generateUrl('carosserie_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création carosserie
     * @param CtCarosserie $_carosserie The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtCarosserie $_carosserie)
    {
        $_form = $this->createForm(CtCarosserieType::class, $_carosserie, array(
            'action' => $this->generateUrl('carosserie_update', array('id' => $_carosserie->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression carosserie
     * @param Request $_request requête
     * @param CtCarosserie $_carosserie
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtCarosserie $_carosserie)
    {
        // Récupérer manager
        $_carosserie_manager = $this->get(ServiceName::SRV_METIER_CAROSSERIE);

        $_form = $this->createDeleteForm($_carosserie);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_carosserie_manager->deleteCtCarosserie($_carosserie);

            $_carosserie_manager->setFlash('success', 'Carrosserie supprimée');
        }

        return $this->redirectToRoute('carosserie_index');
    }

    /**
     * Création formulaire de suppression carosserie
     * @param CtCarosserie $_carosserie The Carosserie entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtCarosserie $_carosserie)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('carosserie_delete', array('id' => $_carosserie->getId())))
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
        $_carosserie_manager = $this->get(ServiceName::SRV_METIER_CAROSSERIE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_carosserie_manager->deleteGroupCtCarosserie($_ids);
        }

        $_carosserie_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('carosserie_index'));
    }
}
