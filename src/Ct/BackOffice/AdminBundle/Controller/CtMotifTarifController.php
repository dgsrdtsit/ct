<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtMotifTarif;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtMotifTarifType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtMotif;

/**
 * Class MotifController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtMotifTarifController extends Controller
{
    /**
     * Afficher tout les motifs
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_motif_manager       = $this->get(ServiceName::SRV_METIER_MOTIF);
        $_motif_tarif_manager = $this->get(ServiceName::SRV_METIER_MOTIF_TARIF);

        // Récupérer tout les tarifs motif
        $ret_motifs = $_motif_tarif_manager->getAllCtMotifTarifByMotif();

        return $this->render('AdminBundle:CtMotifTarif:index.html.twig', array(
            'motifs' => $ret_motifs,
        ));
    }

    /**
     * Affichage page modification motif
     *
     * @param CtMotifTarif $_motif_tarif_
     *
     * @return Render page
     */
    public function editAction(CtMotifTarif $_motif_tarif)
    {
        if (!$_motif_tarif) {
            throw $this->createNotFoundException('Unable to find MotifTarif entity.');
        }

        $_edit_form = $this->createEditForm($_motif_tarif);

        return $this->render('AdminBundle:CtMotifTarif:edit.html.twig', array(
            'motif' => $_motif_tarif,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création motif
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_motif_tarif_manager = $this->get(ServiceName::SRV_METIER_MOTIF_TARIF);

        $_motif_tarif = new CtMotifTarif();
        $_form       = $this->createCreateForm($_motif_tarif);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {

            $_date_time = new \DateTime("now");
            $_current_year = $_date_time->format('Y');
            $_motif_tarif->setMtfTrfDate($_current_year);

            // Enregistrement motif_tarif
            $_return = $_motif_tarif_manager->saveCtMotifTarif($_motif_tarif, 'new');

            if ($_return) {
                $_motif_tarif_manager->setFlash('success', "Motif Tarif ajoutée");

                return $this->redirect($this->generateUrl('motif_tarif_index'));
            }

        }

        return $this->render('AdminBundle:CtMotifTarif:add.html.twig', array(
            'motif_tarif' => $_motif_tarif,
            'form'       => $_form->createView(),
        ));
    }

    /**
     * Modification motif
     * @param Request $_request requête
     * @param CtMotifTarif $_motif_tarif
     * @return Render page
     */
    public function updateAction(Request $_request, CtMotifTarif $_motif_tarif)
    {
        // Récupérer manager
        $_motif_tarif_manager = $this->get(ServiceName::SRV_METIER_MOTIF_TARIF);

        if (!$_motif_tarif) {
            throw $this->createNotFoundException('Unable to find Motif entity.');
        }

        $_edit_form = $this->createEditForm($_motif_tarif);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_motif_tarif_manager->saveCtMotifTarif($_motif_tarif, 'update');

            $_motif_tarif_manager->setFlash('success', "MotifTarif modifiée");

            return $this->redirect($this->generateUrl('motif_tarif_index'));
        }

        return $this->render('AdminBundle:CtMotifTarif:edit.html.twig', array(
            'motif' => $_motif_tarif,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition motif
     * @param CtMotifTarif $_motif_tarif The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtMotifTarif $_motif_tarif)
    {
        $_form = $this->createForm(CtMotifTarifType::class, $_motif_tarif, array(
            'action' => $this->generateUrl('motif_tarif_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création motif
     * @param CtMotifTarif $_motif_tarif_ The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtMotifTarif $_motif_tarif)
    {
        $_form = $this->createForm(CtMotifTarifType::class, $_motif_tarif, array(
            'action' => $this->generateUrl('motif_tarif_update', array('id' => $_motif_tarif->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression motif
     * @param Request $_request requête
     * @param CtMotifTarif $_motif_tarif
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtMotifTarif $_motif_tarif)
    {
        // Récupérer manager
        $_motif_tarif_manager = $this->get(ServiceName::SRV_METIER_MOTIF_TARIF);

        $_form = $this->createDeleteForm($_motif_tarif);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression motif
            $_motif_tarif_manager->deleteCtMotifTarif($_motif_tarif);

            $_motif_tarif_manager->setFlash('success', 'Motif tarif supprimée');
        }

        return $this->redirectToRoute('motif_tarif_index');
    }

    /**
     * Création formulaire de suppression motif
     * @param CtMotifTarif $_motif_tarif The Motif entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtMotifTarif $_motif_tarif)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('motif_tarif_delete', array('id' => $_motif_tarif->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste motif
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_motif_tarif_manager = $this->get(ServiceName::SRV_METIER_MOTIF_TARIF);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_motif_tarif_manager->deleteGroupCtMotifTarif($_ids);
        }

        $_motif_tarif_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('motif_tarif_index'));
    }
}
