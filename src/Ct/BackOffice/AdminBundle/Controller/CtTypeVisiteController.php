<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtTypeVisiteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtTypeVisite;

/**
 * Class CtTypeVisiteController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtTypeVisiteController extends Controller
{
    /**
     * Afficher tout les types visite
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_type_visite_manager = $this->get(ServiceName::SRV_METIER_TYPE_VISITE);

        // Récupérer tout les carosséries
        $_type_visites = $_type_visite_manager->getAllCtTypeVisiteByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtTypeVisite:index.html.twig', array(
            'type_visites' => $_type_visites,
        ));
    }

    /**
     * Affichage page modification type visite
     * @param CtTypeVisite $_type_visite
     * @return Render page
     */
    public function editAction(CtTypeVisite $_type_visite)
    {
        if (!$_type_visite) {
            throw $this->createNotFoundException('Unable to find TypeVisite entity.');
        }

        $_edit_form = $this->createEditForm($_type_visite);

        return $this->render('AdminBundle:CtTypeVisite:edit.html.twig', array(
            'type_visite' => $_type_visite,
            'edit_form'   => $_edit_form->createView()
        ));
    }

    /**
     * Création type visite
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_type_visite_manager = $this->get(ServiceName::SRV_METIER_TYPE_VISITE);

        $_type_visite = new CtTypeVisite();
        $_form        = $this->createCreateForm($_type_visite);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement carossérie
            $_type_visite_manager->saveCtTypeVisite($_type_visite, 'new');

            $_type_visite_manager->setFlash('success', "Type de visite ajouté");

            return $this->redirect($this->generateUrl('type_visite_index'));
        }

        return $this->render('AdminBundle:CtTypeVisite:add.html.twig', array(
            'type_visite' => $_type_visite,
            'form'        => $_form->createView(),
        ));
    }

    /**
     * Modification type visite
     * @param Request $_request requête
     * @param CtTypeVisite $_type_visite
     * @return Render page
     */
    public function updateAction(Request $_request, CtTypeVisite $_type_visite)
    {
        // Récupérer manager
        $_type_visite_manager = $this->get(ServiceName::SRV_METIER_TYPE_VISITE);

        if (!$_type_visite) {
            throw $this->createNotFoundException('Unable to find TypeVisite entity.');
        }

        $_edit_form = $this->createEditForm($_type_visite);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_type_visite_manager->saveCtTypeVisite($_type_visite, 'update');

            $_type_visite_manager->setFlash('success', "Type de visite modifié");

            return $this->redirect($this->generateUrl('type_visite_index'));
        }

        return $this->render('AdminBundle:CtTypeVisite:edit.html.twig', array(
            'type_visite' => $_type_visite,
            'edit_form'   => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition type visite
     * @param CtTypeVisite $_type_visite The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtTypeVisite $_type_visite)
    {
        $_form = $this->createForm(CtTypeVisiteType::class, $_type_visite, array(
            'action' => $this->generateUrl('type_visite_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création type visite
     * @param CtTypeVisite $_type_visite The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtTypeVisite $_type_visite)
    {
        $_form = $this->createForm(CtTypeVisiteType::class, $_type_visite, array(
            'action' => $this->generateUrl('type_visite_update', array('id' => $_type_visite->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression type visite
     * @param Request $_request requête
     * @param CtTypeVisite $_type_visite
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtTypeVisite $_type_visite)
    {
        // Récupérer manager
        $_type_visite_manager = $this->get(ServiceName::SRV_METIER_TYPE_VISITE);

        $_form = $this->createDeleteForm($_type_visite);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_type_visite_manager->deleteCtTypeVisite($_type_visite);

            $_type_visite_manager->setFlash('success', 'Type de visite supprimé');
        }

        return $this->redirectToRoute('type_visite_index');
    }

    /**
     * Création formulaire de suppression type visite
     * @param CtTypeVisite $_type_visite The TypeVisite entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtTypeVisite $_type_visite)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('type_visite_delete', array('id' => $_type_visite->getId())))
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
        $_type_visite_manager = $this->get(ServiceName::SRV_METIER_TYPE_VISITE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_type_visite_manager->deleteGroupCtTypeVisite($_ids);
        }

        $_type_visite_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('type_visite_index'));
    }
}
