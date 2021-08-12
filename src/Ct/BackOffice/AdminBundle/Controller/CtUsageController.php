<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtUsageTarif;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtUsageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtUsage;

/**
 * Class CtUsageController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtUsageController extends Controller
{
    /**
     * Afficher tout les usages
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_usage_manager = $this->get(ServiceName::SRV_METIER_USAGE);

        // Récupérer tout les usages

        $_usages = $_usage_manager->getAllCtUsage();

        return $this->render('AdminBundle:CtUsage:index.html.twig', array(
            'usages' => $_usages,
        ));
    }

    /**
     * Affichage page modification usage
     *
     * @param CtUsage $_usage
     *
     * @return Render page
     */
    public function editAction(CtUsage $_usage)
    {
        if (!$_usage) {
            throw $this->createNotFoundException('Unable to find Usage entity.');
        }

        $_edit_form = $this->createEditForm($_usage);

        return $this->render('AdminBundle:CtUsage:edit.html.twig', array(
            'usage' => $_usage,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création usage
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_usage_manager = $this->get(ServiceName::SRV_METIER_USAGE);

        $_usage = new CtUsage();
        $_form       = $this->createCreateForm($_usage);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement usage
            $_usage_manager->saveCtUsage($_usage, 'new');

            $_usage_manager->setFlash('success', "Usage ajoutée");

            return $this->redirect($this->generateUrl('usage_index'));
        }

        return $this->render('AdminBundle:CtUsage:add.html.twig', array(
            'usage' => $_usage,
            'form'       => $_form->createView(),
        ));

    }

    /**
     * Modification usage
     * @param Request $_request requête
     * @param CtUsage $_usage
     * @return Render page
     */
    public function updateAction(Request $_request, CtUsage $_usage)
    {
        // Récupérer manager
        $_usage_manager = $this->get(ServiceName::SRV_METIER_USAGE);

        if (!$_usage) {
            throw $this->createNotFoundException('Unable to find Usage entity.');
        }

        $_edit_form = $this->createEditForm($_usage);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_usage_manager->saveCtUsage($_usage, 'update');

            $_usage_manager->setFlash('success', "Usage modifiée");

            return $this->redirect($this->generateUrl('usage_index'));
        }

        return $this->render('AdminBundle:CtUsage:edit.html.twig', array(
            'usage' => $_usage,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition usage
     * @param CtUsage $_usage The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtUsage $_usage)
    {
        $_form = $this->createForm(CtUsageType::class, $_usage, array(
            'action' => $this->generateUrl('usage_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création usage
     * @param CtUsage $_usage The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtUsage $_usage)
    {
        $_form = $this->createForm(CtUsageType::class, $_usage, array(
            'action' => $this->generateUrl('usage_update', array('id' => $_usage->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression usage
     * @param Request $_request requête
     * @param CtUsage $_usage
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtUsage $_usage)
    {
        // Récupérer manager
        $_usage_manager = $this->get(ServiceName::SRV_METIER_USAGE);

        $_form = $this->createDeleteForm($_usage);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression usage
            $_usage_manager->deleteCtUsage($_usage);

            $_usage_manager->setFlash('success', 'Usage supprimée');
        }

        return $this->redirectToRoute('usage_index');
    }

    /**
     * Création formulaire de suppression usage
     * @param CtUsage $_usage The Usage entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtUsage $_usage)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('usage_delete', array('id' => $_usage->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste usage
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_usage_manager = $this->get(ServiceName::SRV_METIER_USAGE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_usage_manager->deleteGroupCtUsage($_ids);
        }

        $_usage_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('usage_index'));
    }
}
