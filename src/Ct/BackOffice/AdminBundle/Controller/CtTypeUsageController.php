<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtTypeUsageTarif;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtTypeUsageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtTypeUsage;

/**
 * Class CtTypeUsageController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtTypeUsageController extends Controller
{
    /**
     * Afficher tout les types usage
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_type_usage_manager = $this->get(ServiceName::SRV_METIER_TYPE_USAGE);

        // Récupérer tout les types usage

        $_type_usages = $_type_usage_manager->getAllCtTypeUsage();

        return $this->render('AdminBundle:CtTypeUsage:index.html.twig', array(
            'type_usages' => $_type_usages,
        ));
    }

    /**
     * Affichage page modification type usage
     *
     * @param CtTypeUsage $_type_usage
     *
     * @return Render page
     */
    public function editAction(CtTypeUsage $_type_usage)
    {
        if (!$_type_usage) {
            throw $this->createNotFoundException('Unable to find TypeUsage entity.');
        }

        $_edit_form = $this->createEditForm($_type_usage);

        return $this->render('AdminBundle:CtTypeUsage:edit.html.twig', array(
            'type_usage' => $_type_usage,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création type usage
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_type_usage_manager = $this->get(ServiceName::SRV_METIER_TYPE_USAGE);

        $_type_usage = new CtTypeUsage();
        $_form       = $this->createCreateForm($_type_usage);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement type_usage
            $_type_usage_manager->saveCtTypeUsage($_type_usage, 'new');

            $_type_usage_manager->setFlash('success', "Type d'usage ajouté");

            return $this->redirect($this->generateUrl('type_usage_index'));
        }

        return $this->render('AdminBundle:CtTypeUsage:add.html.twig', array(
            'type_usage' => $_type_usage,
            'form'       => $_form->createView(),
        ));

    }

    /**
     * Modification type usage
     * @param Request $_request requête
     * @param CtTypeUsage $_type_usage
     * @return Render page
     */
    public function updateAction(Request $_request, CtTypeUsage $_type_usage)
    {
        // Récupérer manager
        $_type_usage_manager = $this->get(ServiceName::SRV_METIER_TYPE_USAGE);

        if (!$_type_usage) {
            throw $this->createNotFoundException('Unable to find Usage entity.');
        }

        $_edit_form = $this->createEditForm($_type_usage);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_type_usage_manager->saveCtTypeUsage($_type_usage, 'update');

            $_type_usage_manager->setFlash('success', "Type d'sage modifié");

            return $this->redirect($this->generateUrl('type_usage_index'));
        }

        return $this->render('AdminBundle:CtTypeUsage:edit.html.twig', array(
            'type_usage' => $_type_usage,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition type usage
     * @param CtTypeUsage $_type_usage The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtTypeUsage $_type_usage)
    {
        $_form = $this->createForm(CtTypeUsageType::class, $_type_usage, array(
            'action' => $this->generateUrl('type_usage_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création type usage
     * @param CtTypeUsage $_type_usage The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtTypeUsage $_type_usage)
    {
        $_form = $this->createForm(CtTypeUsageType::class, $_type_usage, array(
            'action' => $this->generateUrl('type_usage_update', array('id' => $_type_usage->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression type usage
     * @param Request $_request requête
     * @param CtTypeUsage $_type_usage
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtTypeUsage $_type_usage)
    {
        // Récupérer manager
        $_type_usage_manager = $this->get(ServiceName::SRV_METIER_TYPE_USAGE);

        $_form = $this->createDeleteForm($_type_usage);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression type_usage
            $_type_usage_manager->deleteCtTypeUsage($_type_usage);

            $_type_usage_manager->setFlash('success', "Type d'usage supprimée");
        }

        return $this->redirectToRoute('type_usage_index');
    }

    /**
     * Création formulaire de suppression type usage
     * @param CtTypeUsage $_type_usage The Usage entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtTypeUsage $_type_usage)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('type_usage_delete', array('id' => $_type_usage->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste type_usage
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_type_usage_manager = $this->get(ServiceName::SRV_METIER_TYPE_USAGE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_type_usage_manager->deleteGroupCtTypeUsage($_ids);
        }

        $_type_usage_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('type_usage_index'));
    }
}
