<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtSourceEnergieType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtSourceEnergie;

/**
 * Class CtSourceEnergieController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtSourceEnergieController extends Controller
{
    /**
     * Afficher tout les energies
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_energie_manager = $this->get(ServiceName::SRV_METIER_ENERGIE);

        // Récupérer tout les carosséries
        $_energies = $_energie_manager->getAllCtSourceEnergieByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtSourceEnergie:index.html.twig', array(
            'energies' => $_energies,
        ));
    }

    /**
     * Affichage page modification energie
     * @param CtSourceEnergie $_energie
     * @return Render page
     */
    public function editAction(CtSourceEnergie $_energie)
    {
        if (!$_energie) {
            throw $this->createNotFoundException('Unable to find energie entity.');
        }

        $_edit_form = $this->createEditForm($_energie);

        return $this->render('AdminBundle:CtSourceEnergie:edit.html.twig', array(
            'energie' => $_energie,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création energie
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_energie_manager = $this->get(ServiceName::SRV_METIER_ENERGIE);

        $_energie = new CtSourceEnergie();
        $_form       = $this->createCreateForm($_energie);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement carossérie
            $_energie_manager->saveCtSourceEnergie($_energie, 'new');

            $_energie_manager->setFlash('success', "Source énergie ajoutée");

            return $this->redirect($this->generateUrl('energie_index'));
        }

        return $this->render('AdminBundle:CtSourceEnergie:add.html.twig', array(
            'energie' => $_energie,
            'form'       => $_form->createView(),
        ));
    }

    /**
     * Modification energie
     * @param Request $_request requête
     * @param CtSourceEnergie $_energie
     * @return Render page
     */
    public function updateAction(Request $_request, CtSourceEnergie $_energie)
    {
        // Récupérer manager
        $_energie_manager = $this->get(ServiceName::SRV_METIER_ENERGIE);

        if (!$_energie) {
            throw $this->createNotFoundException('Unable to find energie entity.');
        }

        $_edit_form = $this->createEditForm($_energie);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_energie_manager->saveCtSourceEnergie($_energie, 'update');

            $_energie_manager->setFlash('success', "Source énergie modifiée");

            return $this->redirect($this->generateUrl('energie_index'));
        }

        return $this->render('AdminBundle:CtSourceEnergie:edit.html.twig', array(
            'energie' => $_energie,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition energie
     * @param CtSourceEnergie $_energie The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtSourceEnergie $_energie)
    {
        $_form = $this->createForm(CtSourceEnergieType::class, $_energie, array(
            'action' => $this->generateUrl('energie_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création energie
     * @param CtSourceEnergie $_energie The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtSourceEnergie $_energie)
    {
        $_form = $this->createForm(CtSourceEnergieType::class, $_energie, array(
            'action' => $this->generateUrl('energie_update', array('id' => $_energie->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression energie
     * @param Request $_request requête
     * @param CtSourceEnergie $_energie
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtSourceEnergie $_energie)
    {
        // Récupérer manager
        $_energie_manager = $this->get(ServiceName::SRV_METIER_ENERGIE);

        $_form = $this->createDeleteForm($_energie);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_energie_manager->deleteCtSourceEnergie($_energie);

            $_energie_manager->setFlash('success', 'Source énergie supprimée');
        }

        return $this->redirectToRoute('energie_index');
    }

    /**
     * Création formulaire de suppression energie
     * @param CtSourceEnergie $_energie The energie entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtSourceEnergie $_energie)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('energie_delete', array('id' => $_energie->getId())))
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
        $_energie_manager = $this->get(ServiceName::SRV_METIER_ENERGIE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_energie_manager->deleteGroupCtSourceEnergie($_ids);
        }

        $_energie_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('energie_index'));
    }
}
