<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtDroitPtacType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtDroitPtac;

/**
 * Class DroitPtacController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtDroitPtacController extends Controller
{
    /**
     * Afficher tout les droit_ptacs
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_droit_ptac_manager = $this->get(ServiceName::SRV_METIER_DROIT_PTAC);

        // Récupérer tout les droit_ptacs

        $_droit_ptacs = $_droit_ptac_manager->getAllCtDroitPtac();

        return $this->render('AdminBundle:CtDroitPtac:index.html.twig', array(
            'droit_ptacs' => $_droit_ptacs,
        ));
    }

    /**
     * Affichage page modification droit_ptac
     *
     * @param CtDroitPtac $_droit_ptac
     *
     * @return Render page
     */
    public function editAction(CtDroitPtac $_droit_ptac)
    {
        if (!$_droit_ptac) {
            throw $this->createNotFoundException('Unable to find DroitPtac entity.');
        }

        $_edit_form = $this->createEditForm($_droit_ptac);

        return $this->render('AdminBundle:CtDroitPtac:edit.html.twig', array(
            'droit_ptac' => $_droit_ptac,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création droit_ptac
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_droit_ptac_manager = $this->get(ServiceName::SRV_METIER_DROIT_PTAC);

        $_droit_ptac = new CtDroitPtac();
        $_form       = $this->createCreateForm($_droit_ptac);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement droit_ptac
            $_droit_ptac_manager->saveCtDroitPtac($_droit_ptac, 'new');

            $_droit_ptac_manager->setFlash('success', "Droit Ptac ajoutée");

            return $this->redirect($this->generateUrl('droit_ptac_index'));
        }

        return $this->render('AdminBundle:CtDroitPtac:add.html.twig', array(
            'droit_ptac' => $_droit_ptac,
            'form'       => $_form->createView(),
        ));

    }

    /**
     * Modification droit_ptac
     * @param Request $_request requête
     * @param CtDroitPtac $_droit_ptac
     * @return Render page
     */
    public function updateAction(Request $_request, CtDroitPtac $_droit_ptac)
    {
        // Récupérer manager
        $_droit_ptac_manager = $this->get(ServiceName::SRV_METIER_DROIT_PTAC);

        if (!$_droit_ptac) {
            throw $this->createNotFoundException('Unable to find DroitPtac entity.');
        }

        $_edit_form = $this->createEditForm($_droit_ptac);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_droit_ptac_manager->saveCtDroitPtac($_droit_ptac, 'update');

            $_droit_ptac_manager->setFlash('success', "DroitPtac modifiée");

            return $this->redirect($this->generateUrl('droit_ptac_index'));
        }

        return $this->render('AdminBundle:CtDroitPtac:edit.html.twig', array(
            'droit_ptac' => $_droit_ptac,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition droit_ptac
     * @param CtDroitPtac $_droit_ptac The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtDroitPtac $_droit_ptac)
    {
        $_form = $this->createForm(CtDroitPtacType::class, $_droit_ptac, array(
            'action' => $this->generateUrl('droit_ptac_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création droit_ptac
     * @param CtDroitPtac $_droit_ptac The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtDroitPtac $_droit_ptac)
    {
        $_form = $this->createForm(CtDroitPtacType::class, $_droit_ptac, array(
            'action' => $this->generateUrl('droit_ptac_update', array('id' => $_droit_ptac->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression droit_ptac
     * @param Request $_request requête
     * @param CtDroitPtac $_droit_ptac
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtDroitPtac $_droit_ptac)
    {
        // Récupérer manager
        $_droit_ptac_manager = $this->get(ServiceName::SRV_METIER_DROIT_PTAC);

        $_form = $this->createDeleteForm($_droit_ptac);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression droit_ptac
            $_droit_ptac_manager->deleteCtDroitPtac($_droit_ptac);

            $_droit_ptac_manager->setFlash('success', 'DroitPtac supprimée');
        }

        return $this->redirectToRoute('droit_ptac_index');
    }

    /**
     * Création formulaire de suppression droit_ptac
     * @param CtDroitPtac $_droit_ptac The DroitPtac entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtDroitPtac $_droit_ptac)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('droit_ptac_delete', array('id' => $_droit_ptac->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste droit_ptac
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_droit_ptac_manager = $this->get(ServiceName::SRV_METIER_DROIT_PTAC);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_droit_ptac_manager->deleteGroupCtDroitPtac($_ids);
        }

        $_droit_ptac_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('droit_ptac_index'));
    }
}
