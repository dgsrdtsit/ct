<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtMotifTarif;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtMotifType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtMotif;

/**
 * Class MotifController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtMotifController extends Controller
{
    /**
     * Afficher tout les motifs
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_motif_manager = $this->get(ServiceName::SRV_METIER_MOTIF);

        // Récupérer tout les motifs

        $_motifs = $_motif_manager->getAllCtMotif();

        return $this->render('AdminBundle:CtMotif:index.html.twig', array(
            'motifs' => $_motifs,
        ));
    }

    /**
     * Affichage page modification motif
     *
     * @param CtMotif $_motif
     *
     * @return Render page
     */
    public function editAction(CtMotif $_motif)
    {
        if (!$_motif) {
            throw $this->createNotFoundException('Unable to find Motif entity.');
        }

        $_edit_form = $this->createEditForm($_motif);

        return $this->render('AdminBundle:CtMotif:edit.html.twig', array(
            'motif' => $_motif,
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
        $_motif_manager = $this->get(ServiceName::SRV_METIER_MOTIF);

        $_motif = new CtMotif();
        $_form       = $this->createCreateForm($_motif);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement motif
            $_motif_manager->saveCtMotif($_motif, 'new');

            $_motif_manager->setFlash('success', "Motif ajoutée");

            return $this->redirect($this->generateUrl('motif_index'));
        }

        return $this->render('AdminBundle:CtMotif:add.html.twig', array(
            'motif' => $_motif,
            'form'       => $_form->createView(),
        ));

    }

    /**
     * Modification motif
     * @param Request $_request requête
     * @param CtMotif $_motif
     * @return Render page
     */
    public function updateAction(Request $_request, CtMotif $_motif)
    {
        // Récupérer manager
        $_motif_manager = $this->get(ServiceName::SRV_METIER_MOTIF);

        if (!$_motif) {
            throw $this->createNotFoundException('Unable to find Motif entity.');
        }

        $_edit_form = $this->createEditForm($_motif);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_motif_manager->saveCtMotif($_motif, 'update');

            $_motif_manager->setFlash('success', "Motif modifiée");

            return $this->redirect($this->generateUrl('motif_index'));
        }

        return $this->render('AdminBundle:CtMotif:edit.html.twig', array(
            'motif' => $_motif,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition motif
     * @param CtMotif $_motif The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtMotif $_motif)
    {
        $_form = $this->createForm(CtMotifType::class, $_motif, array(
            'action' => $this->generateUrl('motif_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création motif
     * @param CtMotif $_motif The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtMotif $_motif)
    {
        $_form = $this->createForm(CtMotifType::class, $_motif, array(
            'action' => $this->generateUrl('motif_update', array('id' => $_motif->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression motif
     * @param Request $_request requête
     * @param CtMotif $_motif
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtMotif $_motif)
    {
        // Récupérer manager
        $_motif_manager = $this->get(ServiceName::SRV_METIER_MOTIF);

        $_form = $this->createDeleteForm($_motif);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression motif
            $_motif_manager->deleteCtMotif($_motif);

            $_motif_manager->setFlash('success', 'Motif supprimée');
        }

        return $this->redirectToRoute('motif_index');
    }

    /**
     * Création formulaire de suppression motif
     * @param CtMotif $_motif The Motif entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtMotif $_motif)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('motif_delete', array('id' => $_motif->getId())))
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
        $_motif_manager = $this->get(ServiceName::SRV_METIER_MOTIF);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_motif_manager->deleteGroupCtMotif($_ids);
        }

        $_motif_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('motif_index'));
    }
}
