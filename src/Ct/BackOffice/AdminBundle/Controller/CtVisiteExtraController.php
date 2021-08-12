<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtVisiteExtraTarif;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtVisiteExtraType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtVisiteExtra;

/**
 * Class VisiteExtraController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtVisiteExtraController extends Controller
{
    /**
     * Afficher tout les visite_extras
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_visite_extra_manager = $this->get(ServiceName::SRV_METIER_VISITE_EXTRA);

        // Récupérer tout les visite_extras

        $_visite_extras = $_visite_extra_manager->getAllCtVisiteExtra();

        return $this->render('AdminBundle:CtVisiteExtra:index.html.twig', array(
            'visite_extras' => $_visite_extras,
        ));
    }

    /**
     * Affichage page modification visite_extra
     *
     * @param CtVisiteExtra $_visite_extra
     *
     * @return Render page
     */
    public function editAction(CtVisiteExtra $_visite_extra)
    {
        if (!$_visite_extra) {
            throw $this->createNotFoundException('Unable to find VisiteExtra entity.');
        }

        $_edit_form = $this->createEditForm($_visite_extra);

        return $this->render('AdminBundle:CtVisiteExtra:edit.html.twig', array(
            'visite_extra' => $_visite_extra,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création visite_extra
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_visite_extra_manager = $this->get(ServiceName::SRV_METIER_VISITE_EXTRA);

        $_visite_extra = new CtVisiteExtra();
        $_form       = $this->createCreateForm($_visite_extra);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement visite_extra
            $_visite_extra_manager->saveCtVisiteExtra($_visite_extra, 'new');

            $_visite_extra_manager->setFlash('success', "Visite extra ajoutée");

            return $this->redirect($this->generateUrl('visite_extra_index'));
        }

        return $this->render('AdminBundle:CtVisiteExtra:add.html.twig', array(
            'visite_extra' => $_visite_extra,
            'form'       => $_form->createView(),
        ));

    }

    /**
     * Modification visite_extra
     * @param Request $_request requête
     * @param CtVisiteExtra $_visite_extra
     * @return Render page
     */
    public function updateAction(Request $_request, CtVisiteExtra $_visite_extra)
    {
        // Récupérer manager
        $_visite_extra_manager = $this->get(ServiceName::SRV_METIER_VISITE_EXTRA);

        if (!$_visite_extra) {
            throw $this->createNotFoundException('Unable to find VisiteExtra entity.');
        }

        $_edit_form = $this->createEditForm($_visite_extra);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_visite_extra_manager->saveCtVisiteExtra($_visite_extra, 'update');

            $_visite_extra_manager->setFlash('success', "VisiteExtra modifiée");

            return $this->redirect($this->generateUrl('visite_extra_index'));
        }

        return $this->render('AdminBundle:CtVisiteExtra:edit.html.twig', array(
            'visite_extra' => $_visite_extra,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition visite_extra
     * @param CtVisiteExtra $_visite_extra The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtVisiteExtra $_visite_extra)
    {
        $_form = $this->createForm(CtVisiteExtraType::class, $_visite_extra, array(
            'action' => $this->generateUrl('visite_extra_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création visite_extra
     * @param CtVisiteExtra $_visite_extra The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtVisiteExtra $_visite_extra)
    {
        $_form = $this->createForm(CtVisiteExtraType::class, $_visite_extra, array(
            'action' => $this->generateUrl('visite_extra_update', array('id' => $_visite_extra->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression visite_extra
     * @param Request $_request requête
     * @param CtVisiteExtra $_visite_extra
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtVisiteExtra $_visite_extra)
    {
        // Récupérer manager
        $_visite_extra_manager = $this->get(ServiceName::SRV_METIER_VISITE_EXTRA);

        $_form = $this->createDeleteForm($_visite_extra);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression visite_extra
            $_visite_extra_manager->deleteCtVisiteExtra($_visite_extra);

            $_visite_extra_manager->setFlash('success', 'VisiteExtra supprimée');
        }

        return $this->redirectToRoute('visite_extra_index');
    }

    /**
     * Création formulaire de suppression visite_extra
     * @param CtVisiteExtra $_visite_extra The VisiteExtra entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtVisiteExtra $_visite_extra)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('visite_extra_delete', array('id' => $_visite_extra->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste visite_extra
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_visite_extra_manager = $this->get(ServiceName::SRV_METIER_VISITE_EXTRA);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_visite_extra_manager->deleteGroupCtVisiteExtra($_ids);
        }

        $_visite_extra_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('visite_extra_index'));
    }
}
