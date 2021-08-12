<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtConstAvDedCarac;
use Ct\Service\MetierManagerBundle\Form\CtConstatationAvDedouanementCaracType;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;



/**
 * Class CtConstatationAvDedouanementController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtConstatationAvDedouanementCaracController extends Controller
{
    /**
     * Afficher tout les constatation avant dedouanements
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_const_av_ded_carac_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED);

        // Récupérer tout les carosséries
        $_constatations = $_const_av_ded_carac_manager->getAllCtConstatationAvDedouanementByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtConstatationAvDedouanementCarac:index.html.twig', array(
            'constatations' => $_constatations,
        ));
    }

    /**
     * Affichage page modification constatation avant dedouanement
     * @param CtConstAvDedCarac $_const_av_ded_carac
     * @return Render page
     */
    public function editAction(CtConstAvDedCarac $_const_av_ded_carac)
    {
        if (!$_const_av_ded_carac) {
            throw $this->createNotFoundException('Unable to find constatation avant dedouanement entity.');
        }

        $_edit_form = $this->createEditForm($_const_av_ded_carac);

        return $this->render('AdminBundle:CtConstatationAvDedouanementCarac:edit.html.twig', array(
            'constatation avant dedouanement' => $_const_av_ded_carac,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création constatation avant dedouanement
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_const_av_ded_carac_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED_CARAC);

        $_const_av_ded_carac = new CtConstAvDedCarac();
        $_form       = $this->createCreateForm($_const_av_ded_carac);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement carossérie
            $_const_av_ded_carac_manager->saveCtConstatationAvDedouanement($_const_av_ded_carac, 'new');

            $_const_av_ded_carac_manager->setFlash('success', "Constatation avant dedouanement ajoutée");

            return $this->redirect($this->generateUrl('const_av_ded_index'));
        }

        return $this->render('AdminBundle:CtConstatationAvDedouanementCarac:add.html.twig', array(
            'constatation avant dedouanement_carac' => $_const_av_ded_carac,
            'form'       => $_form->createView(),
        ));
    }

    /**
     * Modification constatation avant dedouanement
     * @param Request $_request requête
     * @param CtConstAvDedCarac $_const_av_ded_carac
     * @return Render page
     */
    public function updateAction(Request $_request, CtConstAvDedCarac $_const_av_ded_carac)
    {
        // Récupérer manager
        $_const_av_ded_carac_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED_CARAC);

        if (!$_const_av_ded_carac) {
            throw $this->createNotFoundException('Unable to find constatation avant dedouanement entity.');
        }

        $_edit_form = $this->createEditForm($_const_av_ded_carac);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_const_av_ded_carac_manager->saveCtConstatationAvDedouanementCarac($_const_av_ded_carac, 'update');

            $_const_av_ded_carac_manager->setFlash('success', "Constatation avant dedouanement modifiée");

            return $this->redirect($this->generateUrl('const_av_ded_index'));
        }

        return $this->render('AdminBundle:CtConstatationAvDedouanementCarac:edit.html.twig', array(
            'const_av_ded' => $_const_av_ded_carac,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition constatation avant dedouanement
     * @param CtConstAvDedCarac $_const_av_ded_carac The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtConstAvDedCarac $_const_av_ded_carac)
    {
        $_form = $this->createForm(CtConstatationAvDedouanementCaracType::class, $_const_av_ded_carac, array(
            'action' => $this->generateUrl('const_av_ded_carac_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création constatation avant dedouanement
     * @param CtConstAvDedCarac $_const_av_ded_carac The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtConstAvDedCarac $_const_av_ded_carac)
    {
        $_form = $this->createForm(CtConstatationAvDedouanementCaracType::class, $_const_av_ded_carac, array(
            'action' => $this->generateUrl('const_av_ded_carac_update', array('id' => $_const_av_ded_carac->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression constatation avant dedouanement
     * @param Request $_request requête
     * @param CtConstAvDedCarac $_const_av_ded_carac
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtConstAvDedCarac $_const_av_ded_carac)
    {
        // Récupérer manager
        $_const_av_ded_carac_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED_CARAC);

        $_form = $this->createDeleteForm($_const_av_ded_carac);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_const_av_ded_carac_manager->deleteCtConstatationAvDedouanementCarac($_const_av_ded_carac);

            $_const_av_ded_carac_manager->setFlash('success', 'Constatation avant dedouanement supprimée');
        }

        return $this->redirectToRoute('const_av_ded_carac_index');
    }

    /**
     * Création formulaire de suppression constatation avant dedouanement
     * @param CtConstAvDedCarac $_const_av_ded_carac The constatation avant dedouanement entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtConstAvDedCarac $_const_av_ded_carac)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('const_av_ded_carac_delete', array('id' => $_const_av_ded_carac->getId())))
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
        $_const_av_ded_carac_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED_CARAC);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_const_av_ded_carac_manager->deleteGroupCtConstatationAvDedouanementCarac($_ids);
        }

        $_const_av_ded_carac_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('const_av_ded_carac_index'));
    }
}
