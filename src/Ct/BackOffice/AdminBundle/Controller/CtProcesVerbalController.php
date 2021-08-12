<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtProcesVerbalTarif;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtProcesVerbalType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtProcesVerbal;

/**
 * Class ProcesVerbalController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtProcesVerbalController extends Controller
{
    /**
     * Afficher tout les proces_verbals
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_proces_verbal_manager = $this->get(ServiceName::SRV_METIER_PROCES_VERBAL);

        // Récupérer tout les proces_verbals

        $_proces_verbals = $_proces_verbal_manager->getAllCtProcesVerbal();

        return $this->render('AdminBundle:CtProcesVerbal:index.html.twig', array(
            'proces_verbals' => $_proces_verbals,
        ));
    }

    /**
     * Affichage page modification proces_verbal
     *
     * @param CtProcesVerbal $_proces_verbal
     *
     * @return Render page
     */
    public function editAction(CtProcesVerbal $_proces_verbal)
    {
        if (!$_proces_verbal) {
            throw $this->createNotFoundException('Unable to find ProcesVerbal entity.');
        }

        $_edit_form = $this->createEditForm($_proces_verbal);

        return $this->render('AdminBundle:CtProcesVerbal:edit.html.twig', array(
            'proces_verbal' => $_proces_verbal,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création proces_verbal
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_proces_verbal_manager = $this->get(ServiceName::SRV_METIER_PROCES_VERBAL);

        $_proces_verbal = new CtProcesVerbal();
        $_form       = $this->createCreateForm($_proces_verbal);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement proces_verbal
            $_proces_verbal_manager->saveCtProcesVerbal($_proces_verbal, 'new');

            $_proces_verbal_manager->setFlash('success', "Proces Verbal ajoutée");

            return $this->redirect($this->generateUrl('proces_verbal_index'));
        }

        return $this->render('AdminBundle:CtProcesVerbal:add.html.twig', array(
            'proces_verbal' => $_proces_verbal,
            'form'       => $_form->createView(),
        ));

    }

    /**
     * Modification proces_verbal
     * @param Request $_request requête
     * @param CtProcesVerbal $_proces_verbal
     * @return Render page
     */
    public function updateAction(Request $_request, CtProcesVerbal $_proces_verbal)
    {
        // Récupérer manager
        $_proces_verbal_manager = $this->get(ServiceName::SRV_METIER_PROCES_VERBAL);

        if (!$_proces_verbal) {
            throw $this->createNotFoundException('Unable to find ProcesVerbal entity.');
        }

        $_edit_form = $this->createEditForm($_proces_verbal);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_proces_verbal_manager->saveCtProcesVerbal($_proces_verbal, 'update');

            $_proces_verbal_manager->setFlash('success', "Proces Verbal modifiée");

            return $this->redirect($this->generateUrl('proces_verbal_index'));
        }

        return $this->render('AdminBundle:CtProcesVerbal:edit.html.twig', array(
            'proces_verbal' => $_proces_verbal,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition proces_verbal
     * @param CtProcesVerbal $_proces_verbal The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtProcesVerbal $_proces_verbal)
    {
        $_form = $this->createForm(CtProcesVerbalType::class, $_proces_verbal, array(
            'action' => $this->generateUrl('proces_verbal_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création proces_verbal
     * @param CtProcesVerbal $_proces_verbal The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtProcesVerbal $_proces_verbal)
    {
        $_form = $this->createForm(CtProcesVerbalType::class, $_proces_verbal, array(
            'action' => $this->generateUrl('proces_verbal_update', array('id' => $_proces_verbal->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression proces_verbal
     * @param Request $_request requête
     * @param CtProcesVerbal $_proces_verbal
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtProcesVerbal $_proces_verbal)
    {
        // Récupérer manager
        $_proces_verbal_manager = $this->get(ServiceName::SRV_METIER_PROCES_VERBAL);

        $_form = $this->createDeleteForm($_proces_verbal);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression proces_verbal
            $_proces_verbal_manager->deleteCtProcesVerbal($_proces_verbal);

            $_proces_verbal_manager->setFlash('success', 'Proces Verbal supprimée');
        }

        return $this->redirectToRoute('proces_verbal_index');
    }

    /**
     * Création formulaire de suppression proces_verbal
     * @param CtProcesVerbal $_proces_verbal The ProcesVerbal entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtProcesVerbal $_proces_verbal)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('proces_verbal_delete', array('id' => $_proces_verbal->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste proces_verbal
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_proces_verbal_manager = $this->get(ServiceName::SRV_METIER_PROCES_VERBAL);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_proces_verbal_manager->deleteGroupCtProcesVerbal($_ids);
        }

        $_proces_verbal_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('proces_verbal_index'));
    }
}
