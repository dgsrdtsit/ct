<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtTypeConstatationAvDedouanementType;
use Ct\Service\MetierManagerBundle\Entity\CtConstAvDedType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class CtTypeConstatationAvDedouanementController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtTypeConstatationAvDedouanementController extends Controller
{
    /**
     * Afficher tous les types constatations avant dédouanement
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_const_av_ded_manager = $this->get(ServiceName::SRV_METIER_TYPE_CONST_AV_DED);

        // Récupérer tout les carosséries
        $_constatations = $_const_av_ded_manager->getAllCtTypeConstatationAvDedouanementByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtTypeConstatationAvDedouanement:index.html.twig', array(
            'constatations' => $_constatations,
        ));
    }

    /**
     * Affichage page modification type constatation avant dédouanement
     * @param CtConstAvDedType $_type_const_av_ded
     * @return Render page
     */
    public function editAction(CtConstAvDedType $_type_const_av_ded)
    {
        if (!$_type_const_av_ded) {
            throw $this->createNotFoundException('Unable to find CtTypeConstatationAvDedouanement entity.');
        }

        $_edit_form = $this->createEditForm($_type_const_av_ded);

        return $this->render('AdminBundle:CtTypeConstatationAvDedouanement:edit.html.twig', array(
            'type_const_av_ded' => $_type_const_av_ded,
            'edit_form'         => $_edit_form->createView()
        ));
    }

    /**
     * Création type constatation avant dédouanement
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_const_av_ded_manager = $this->get(ServiceName::SRV_METIER_TYPE_CONST_AV_DED);

        $_type_const_av_ded = new CtConstAvDedType();
        $_form       = $this->createCreateForm($_type_const_av_ded);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement carossérie
            $_const_av_ded_manager->saveCtTypeConstatationAvDedouanement($_type_const_av_ded, 'new');

            $_const_av_ded_manager->setFlash('success', "Type constatation avant dédouanement ajouté");

            return $this->redirect($this->generateUrl('type_const_av_ded_index'));
        }

        return $this->render('AdminBundle:CtTypeConstatationAvDedouanement:add.html.twig', array(
            'const_av_ded_type' => $_type_const_av_ded,
            'form'              => $_form->createView(),
        ));
    }

    /**
     * Modification type constatation avant dédouanement
     * @param Request $_request requête
     * @param CtConstAvDedType $_type_const_av_ded
     * @return Render page
     */
    public function updateAction(Request $_request, CtConstAvDedType $_type_const_av_ded)
    {
        // Récupérer manager
        $_const_av_ded_manager = $this->get(ServiceName::SRV_METIER_TYPE_CONST_AV_DED);

        if (!$_type_const_av_ded) {
            throw $this->createNotFoundException('Unable to find CtTypeConstatationAvDedouanement entity.');
        }

        $_edit_form = $this->createEditForm($_type_const_av_ded);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_const_av_ded_manager->saveCtTypeConstatationAvDedouanement($_type_const_av_ded, 'update');

            $_const_av_ded_manager->setFlash('success', "Type constatation avant dédouanement modifié");

            return $this->redirect($this->generateUrl('type_const_av_ded_index'));
        }

        return $this->render('AdminBundle:CtTypeConstatationAvDedouanement:edit.html.twig', array(
            'const_av_ded' => $_type_const_av_ded,
            'edit_form'    => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition type constatation avant dédouanement
     * @param CtConstAvDedType $_type_const_av_ded The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtConstAvDedType $_type_const_av_ded)
    {
        $_form = $this->createForm(CtTypeConstatationAvDedouanementType::class, $_type_const_av_ded, array(
            'action' => $this->generateUrl('type_const_av_ded_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création constatation avant dédouanement
     * @param CtConstAvDedType $_type_const_av_ded The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtConstAvDedType $_type_const_av_ded)
    {
        $_form = $this->createForm(CtTypeConstatationAvDedouanementType::class, $_type_const_av_ded, array(
            'action' => $this->generateUrl('type_const_av_ded_update', array('id' => $_type_const_av_ded->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression type constatation avant dédouanement
     * @param Request $_request requête
     * @param CtConstAvDedType $_type_const_av_ded
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtConstAvDedType $_type_const_av_ded)
    {
        // Récupérer manager
        $_const_av_ded_manager = $this->get(ServiceName::SRV_METIER_TYPE_CONST_AV_DED);

        $_form = $this->createDeleteForm($_type_const_av_ded);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_const_av_ded_manager->deleteCtTypeConstatationAvDedouanement($_type_const_av_ded);

            $_const_av_ded_manager->setFlash('success', 'Type constatation avant dédouanement supprimé');
        }

        return $this->redirectToRoute('type_const_av_ded_index');
    }

    /**
     * Création formulaire de suppression type constatation avant dédouanement
     * @param CtConstAvDedType $_type_const_av_ded The constatation av ded entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtConstAvDedType $_type_const_av_ded)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('type_const_av_ded_delete', array('id' => $_type_const_av_ded->getId())))
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
        $_const_av_ded_manager = $this->get(ServiceName::SRV_METIER_TYPE_CONST_AV_DED);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_const_av_ded_manager->deleteGroupCtTypeConstatationAvDedouanement($_ids);
        }

        $_const_av_ded_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('type_const_av_ded_index'));
    }
}
