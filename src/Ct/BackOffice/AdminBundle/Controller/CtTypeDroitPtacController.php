<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtTypeDroitPtacType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtTypeDroitPtac;

/**
 * Class CtTypeDroitPtacController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtTypeDroitPtacController extends Controller
{
    /**
     * Afficher tout les types droit_ptac
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_type_droit_ptac_manager = $this->get(ServiceName::SRV_METIER_TYPE_DROIT_PTAC);

        // Récupérer tout les carosséries
        $_type_droit_ptacs = $_type_droit_ptac_manager->getAllCtTypeDroitPtacByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtTypeDroitPtac:index.html.twig', array(
            'type_droit_ptacs' => $_type_droit_ptacs,
        ));
    }

    /**
     * Affichage page modification type droit_ptac
     * @param CtTypeDroitPtac $_type_droit_ptac
     * @return Render page
     */
    public function editAction(CtTypeDroitPtac $_type_droit_ptac)
    {
        if (!$_type_droit_ptac) {
            throw $this->createNotFoundException('Unable to find TypeDroitPtac entity.');
        }

        $_edit_form = $this->createEditForm($_type_droit_ptac);

        return $this->render('AdminBundle:CtTypeDroitPtac:edit.html.twig', array(
            'type_droit_ptac' => $_type_droit_ptac,
            'edit_form'   => $_edit_form->createView()
        ));
    }

    /**
     * Création type droit_ptac
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_type_droit_ptac_manager = $this->get(ServiceName::SRV_METIER_TYPE_DROIT_PTAC);

        $_type_droit_ptac = new CtTypeDroitPtac();
        $_form        = $this->createCreateForm($_type_droit_ptac);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement carossérie
            $_type_droit_ptac_manager->saveCtTypeDroitPtac($_type_droit_ptac, 'new');

            $_type_droit_ptac_manager->setFlash('success', "Type de droit_ptac ajouté");

            return $this->redirect($this->generateUrl('type_droit_ptac_index'));
        }

        return $this->render('AdminBundle:CtTypeDroitPtac:add.html.twig', array(
            'type_droit_ptac' => $_type_droit_ptac,
            'form'        => $_form->createView(),
        ));
    }

    /**
     * Modification type droit_ptac
     * @param Request $_request requête
     * @param CtTypeDroitPtac $_type_droit_ptac
     * @return Render page
     */
    public function updateAction(Request $_request, CtTypeDroitPtac $_type_droit_ptac)
    {
        // Récupérer manager
        $_type_droit_ptac_manager = $this->get(ServiceName::SRV_METIER_TYPE_DROIT_PTAC);

        if (!$_type_droit_ptac) {
            throw $this->createNotFoundException('Unable to find TypeDroitPtac entity.');
        }

        $_edit_form = $this->createEditForm($_type_droit_ptac);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_type_droit_ptac_manager->saveCtTypeDroitPtac($_type_droit_ptac, 'update');

            $_type_droit_ptac_manager->setFlash('success', "Type de droit_ptac modifié");

            return $this->redirect($this->generateUrl('type_droit_ptac_index'));
        }

        return $this->render('AdminBundle:CtTypeDroitPtac:edit.html.twig', array(
            'type_droit_ptac' => $_type_droit_ptac,
            'edit_form'   => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition type droit_ptac
     * @param CtTypeDroitPtac $_type_droit_ptac The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtTypeDroitPtac $_type_droit_ptac)
    {
        $_form = $this->createForm(CtTypeDroitPtacType::class, $_type_droit_ptac, array(
            'action' => $this->generateUrl('type_droit_ptac_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création type droit_ptac
     * @param CtTypeDroitPtac $_type_droit_ptac The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtTypeDroitPtac $_type_droit_ptac)
    {
        $_form = $this->createForm(CtTypeDroitPtacType::class, $_type_droit_ptac, array(
            'action' => $this->generateUrl('type_droit_ptac_update', array('id' => $_type_droit_ptac->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression type droit_ptac
     * @param Request $_request requête
     * @param CtTypeDroitPtac $_type_droit_ptac
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtTypeDroitPtac $_type_droit_ptac)
    {
        // Récupérer manager
        $_type_droit_ptac_manager = $this->get(ServiceName::SRV_METIER_TYPE_DROIT_PTAC);

        $_form = $this->createDeleteForm($_type_droit_ptac);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_type_droit_ptac_manager->deleteCtTypeDroitPtac($_type_droit_ptac);

            $_type_droit_ptac_manager->setFlash('success', 'Type de droit_ptac supprimé');
        }

        return $this->redirectToRoute('type_droit_ptac_index');
    }

    /**
     * Création formulaire de suppression type droit_ptac
     * @param CtTypeDroitPtac $_type_droit_ptac The TypeDroitPtac entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtTypeDroitPtac $_type_droit_ptac)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('type_droit_ptac_delete', array('id' => $_type_droit_ptac->getId())))
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
        $_type_droit_ptac_manager = $this->get(ServiceName::SRV_METIER_TYPE_DROIT_PTAC);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_type_droit_ptac_manager->deleteGroupCtTypeDroitPtac($_ids);
        }

        $_type_droit_ptac_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('type_droit_ptac_index'));
    }
}
