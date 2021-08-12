<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtImprimeTechType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Ct\Service\MetierManagerBundle\Entity\CtImprimeTech;

/**
 * Class CtImprimeTechController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtImprimeTechController extends Controller
{
    /**
     * Afficher tout les imprimés tech
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_imprime_tech_manager = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);

        // Récupérer tout les imprimés tech
        $_imprime_tech = $_imprime_tech_manager->getAllCtImprimeTechByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtImprimeTech:index.html.twig', array(
            'imprime_techs' => $_imprime_tech,
        ));
    }

    /**
     * Affichage page modification imprime tech
     * @param CtImprimeTech $_imprime_tech
     * @return Render page
     */
    public function editAction(CtImprimeTech $_imprime_tech)
    {
        if (!$_imprime_tech) {
            throw $this->createNotFoundException('Unable to find imprime tech entity.');
        }

        $_edit_form = $this->createEditForm($_imprime_tech);

        return $this->render('AdminBundle:CtImprimeTech:edit.html.twig', array(
            'imprime_tech' => $_imprime_tech,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création imprime tech
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_imprime_tech_manager = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);

        $_imprime_tech  = new CtImprimeTech();
        $_form          = $this->createCreateForm($_imprime_tech);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement carossérie
            $_imprime_tech_manager->saveCtImprimeTech($_imprime_tech, 'new');
            $_imprime_tech_manager->setFlash('success', "Imprimé technique ajouté");
            return $this->redirect($this->generateUrl('imprime_tech_index'));
        }

        return $this->render('AdminBundle:CtImprimeTech:add.html.twig', array(
            'imprime_tech' => $_imprime_tech,
            'form'       => $_form->createView(),
        ));
    }

    /**
     * Modification imprimé tech
     * @param Request $_request requête
     * @param CtImprimeTech $_imprime_tech
     * @return Render page
     */
    public function updateAction(Request $_request, CtImprimeTech $_imprime_tech)
    {
        // Récupérer manager
        $_imprime_tech_manager = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);

        if (!$_imprime_tech) {
            throw $this->createNotFoundException('Unable to find imprime tech entity.');
        }

        $_edit_form = $this->createEditForm($_imprime_tech);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_imprime_tech_manager->saveCtImprimeTech($_imprime_tech, 'update');
            $_imprime_tech_manager->setFlash('success', "Imprimé technique modifié");
            return $this->redirect($this->generateUrl('imprime_tech_index'));
        }

        return $this->render('AdminBundle:CtImprimeTech:edit.html.twig', array(
            'imprime_tech' => $_imprime_tech,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition imprimé tech
     * @param CtImprimeTech $_imprime_tech The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtImprimeTech $_imprime_tech)
    {
        $_form = $this->createForm(CtImprimeTechType::class, $_imprime_tech, array(
            'action' => $this->generateUrl('imprime_tech_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création imprimé tech
     * @param CtImprimeTech $_imprime_tech The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtImprimeTech $_imprime_tech)
    {
        $_form = $this->createForm(CtImprimeTechType::class, $_imprime_tech, array(
            'action' => $this->generateUrl('imprime_tech_update', array('id' => $_imprime_tech->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression marque
     * @param Request $_request requête
     * @param CtImprimeTech $_imprime_tech
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtImprimeTech $_imprime_tech)
    {
        // Récupérer manager
        $_imprime_tech_manager = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);

        $_form = $this->createDeleteForm($_imprime_tech);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_imprime_tech_manager->deleteCtImprimeTech($_imprime_tech);

            $_imprime_tech_manager->setFlash('success', 'Imprimé technique supprimé');
        }

        return $this->redirectToRoute('imprime_tech_index');
    }

    /**
     * Création formulaire de suppression imprimé tech
     * @param CtImprimeTech $_imprime_tech The imprimé tech entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtImprimeTech $_imprime_tech)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('imprime_tech_delete', array('id' => $_imprime_tech->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste imprimé tech
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_imprime_tech_manager = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_imprime_tech_manager->deleteGroupCtImprimeTech($_ids);
        }

        $_imprime_tech_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('imprime_tech_index'));
    }
}
