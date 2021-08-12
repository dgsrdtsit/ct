<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtProvinceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtProvince;

/**
 * Class CtProvinceController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtProvinceController extends Controller
{
    /**
     * Afficher tout les province
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_province_manager = $this->get(ServiceName::SRV_METIER_PROVINCE);

        // Récupérer tout les province
        $_provinces = $_province_manager->getAllCtProvinceByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtProvince:index.html.twig', array(
            'provinces' => $_provinces,
        ));
    }

    /**
     * Affichage page modification province
     * @param CtProvince $_province
     * @return Render page
     */
    public function editAction(CtProvince $_province)
    {
        if (!$_province) {
            throw $this->createNotFoundException('Unable to find CtProvince entity.');
        }

        $_edit_form = $this->createEditForm($_province);

        return $this->render('AdminBundle:CtProvince:edit.html.twig', array(
            'province'  => $_province,
            'edit_form' => $_edit_form->createView()
        ));
    }

    /**
     * Création province
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_province_manager = $this->get(ServiceName::SRV_METIER_PROVINCE);

        $_province = new CtProvince();
        $_form     = $this->createCreateForm($_province);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement province
            $_province_manager->saveCtProvince($_province, 'new');

            $_province_manager->setFlash('success', "Province ajouté");

            return $this->redirect($this->generateUrl('province_index'));
        }

        return $this->render('AdminBundle:CtProvince:add.html.twig', array(
            'province' => $_province,
            'form'   => $_form->createView(),
        ));
    }

    /**
     * Modification province
     * @param Request $_request requête
     * @param CtProvince $_province
     * @return Render page
     */
    public function updateAction(Request $_request, CtProvince $_province)
    {
        // Récupérer manager
        $_province_manager = $this->get(ServiceName::SRV_METIER_PROVINCE);

        if (!$_province) {
            throw $this->createNotFoundException('Unable to find CtProvince entity.');
        }

        $_edit_form = $this->createEditForm($_province);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_province_manager->updateCtProvince($_province);

            $_province_manager->setFlash('success', "Province modifié");

            return $this->redirect($this->generateUrl('province_index'));
        }

        return $this->render('AdminBundle:CtProvince:edit.html.twig', array(
            'province'  => $_province,
            'edit_form' => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition province
     * @param CtProvince $_province The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtProvince $_province)
    {
        $_form = $this->createForm(CtProvinceType::class, $_province, array(
            'action' => $this->generateUrl('province_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création province
     * @param CtProvince $_province The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtProvince $_province)
    {
        $_form = $this->createForm(CtProvinceType::class, $_province, array(
            'action' => $this->generateUrl('province_update', array('id' => $_province->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression province
     * @param Request $_request requête
     * @param CtProvince $_province
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtProvince $_province)
    {
        // Récupérer manager
        $_province_manager = $this->get(ServiceName::SRV_METIER_PROVINCE);

        $_form = $this->createDeleteForm($_province);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression province
            $_province_manager->deleteCtProvince($_province);

            $_province_manager->setFlash('success', 'Province supprimé');
        }

        return $this->redirectToRoute('province_index');
    }

    /**
     * Création formulaire de suppression province
     * @param CtProvince $_province The CtProvince entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtProvince $_province)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('province_delete', array('id' => $_province->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste province
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_province_manager = $this->get(ServiceName::SRV_METIER_PROVINCE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_province_manager->deleteGroupCtProvince($_ids);
        }

        $_province_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('province_index'));
    }
}
