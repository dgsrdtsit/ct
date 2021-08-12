<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtVisiteExtraTarif;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtVisiteExtraTarifType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtVisiteExtra;

/**
 * Class VisiteExtraController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtVisiteExtraTarifController extends Controller
{
    /**
     * Afficher tout les visite_extras
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager

        $_visite_extra_tarif_manager = $this->get(ServiceName::SRV_METIER_VISITE_EXTRA_TARIF);

        // Récupérer tout les tarifs visite_extra
        $ret_visite_extras = $_visite_extra_tarif_manager->getAllCtVisiteExtraTarifByVisiteExtra();

        return $this->render('AdminBundle:CtVisiteExtraTarif:index.html.twig', array(
            'visite_extras' => $ret_visite_extras,
        ));
    }

    /**
     * Affichage page modification visite_extra
     *
     * @param CtVisiteExtraTarif $_visite_extra_tarif_
     *
     * @return Render page
     */
    public function editAction(CtVisiteExtraTarif $_visite_extra_tarif)
    {
        if (!$_visite_extra_tarif) {
            throw $this->createNotFoundException('Unable to find VisiteExtraTarif entity.');
        }

        $_edit_form = $this->createEditForm($_visite_extra_tarif);

        return $this->render('AdminBundle:CtVisiteExtraTarif:edit.html.twig', array(
            'visite_extra' => $_visite_extra_tarif,
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
        $_visite_extra_tarif_manager = $this->get(ServiceName::SRV_METIER_VISITE_EXTRA_TARIF);

        $_visite_extra_tarif = new CtVisiteExtraTarif();
        $_form       = $this->createCreateForm($_visite_extra_tarif);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {

            $_date_time = new \DateTime("now");
            $_current_year = $_date_time->format('Y');
            $_visite_extra_tarif->setVetAnnee($_current_year);

            // Enregistrement visite_extra_tarif
            $_return = $_visite_extra_tarif_manager->saveCtVisiteExtraTarif($_visite_extra_tarif, 'new');

            if ($_return) {
                $_visite_extra_tarif_manager->setFlash('success', "Visite Extra Tarif ajoutée");

                return $this->redirect($this->generateUrl('visite_extra_tarif_index'));
            }

        }

        return $this->render('AdminBundle:CtVisiteExtraTarif:add.html.twig', array(
            'visite_extra_tarif' => $_visite_extra_tarif,
            'form'       => $_form->createView(),
        ));
    }

    /**
     * Modification visite_extra
     * @param Request $_request requête
     * @param CtVisiteExtraTarif $_visite_extra_tarif
     * @return Render page
     */
    public function updateAction(Request $_request, CtVisiteExtraTarif $_visite_extra_tarif)
    {
        // Récupérer manager
        $_visite_extra_tarif_manager = $this->get(ServiceName::SRV_METIER_VISITE_EXTRA_TARIF);

        if (!$_visite_extra_tarif) {
            throw $this->createNotFoundException('Unable to find VisiteExtra entity.');
        }

        $_edit_form = $this->createEditForm($_visite_extra_tarif);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_visite_extra_tarif_manager->saveCtVisiteExtraTarif($_visite_extra_tarif, 'update');

            $_visite_extra_tarif_manager->setFlash('success', "Visite Extra Tarif modifiée");

            return $this->redirect($this->generateUrl('visite_extra_tarif_index'));
        }

        return $this->render('AdminBundle:CtVisiteExtraTarif:edit.html.twig', array(
            'visite_extra' => $_visite_extra_tarif,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition visite_extra
     * @param CtVisiteExtraTarif $_visite_extra_tarif The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtVisiteExtraTarif $_visite_extra_tarif)
    {
        $_form = $this->createForm(CtVisiteExtraTarifType::class, $_visite_extra_tarif, array(
            'action' => $this->generateUrl('visite_extra_tarif_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création visite_extra
     * @param CtVisiteExtraTarif $_visite_extra_tarif_ The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtVisiteExtraTarif $_visite_extra_tarif)
    {
        $_form = $this->createForm(CtVisiteExtraTarifType::class, $_visite_extra_tarif, array(
            'action' => $this->generateUrl('visite_extra_tarif_update', array('id' => $_visite_extra_tarif->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression visite_extra
     * @param Request $_request requête
     * @param CtVisiteExtraTarif $_visite_extra_tarif
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtVisiteExtraTarif $_visite_extra_tarif)
    {
        // Récupérer manager
        $_visite_extra_tarif_manager = $this->get(ServiceName::SRV_METIER_VISITE_EXTRA_TARIF);

        $_form = $this->createDeleteForm($_visite_extra_tarif);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression visite_extra
            $_visite_extra_tarif_manager->deleteCtVisiteExtraTarif($_visite_extra_tarif);

            $_visite_extra_tarif_manager->setFlash('success', 'VisiteExtra tarif supprimée');
        }

        return $this->redirectToRoute('visite_extra_tarif_index');
    }

    /**
     * Création formulaire de suppression visite_extra
     * @param CtVisiteExtraTarif $_visite_extra_tarif The VisiteExtra entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtVisiteExtraTarif $_visite_extra_tarif)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('visite_extra_tarif_delete', array('id' => $_visite_extra_tarif->getId())))
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
        $_visite_extra_tarif_manager = $this->get(ServiceName::SRV_METIER_VISITE_EXTRA_TARIF);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_visite_extra_tarif_manager->deleteGroupCtVisiteExtraTarif($_ids);
        }

        $_visite_extra_tarif_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('visite_extra_tarif_index'));
    }
}
