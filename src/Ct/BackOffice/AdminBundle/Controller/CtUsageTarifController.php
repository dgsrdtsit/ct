<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtUsageTarif;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtUsageTarifType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtUsage;

/**
 * Class UsageController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtUsageTarifController extends Controller
{
    /**
     * Afficher tout les usages
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_usage_tarif_manager = $this->get(ServiceName::SRV_METIER_USAGE_TARIF);

        // Récupérer tout les tarifs genres
        $ret_tarif_usages = $_usage_tarif_manager->getAllCtUsageTarifByUsage();

        return $this->render('AdminBundle:CtUsageTarif:index.html.twig', array(
            'usages' => $ret_tarif_usages,
        ));
    }

    /**
     * Affichage page modification usage
     *
     * @param CtUsageTarif $_usage_tarif_
     *
     * @return Render page
     */
    public function editAction(CtUsageTarif $_usage_tarif)
    {
        if (!$_usage_tarif) {
            throw $this->createNotFoundException('Unable to find UsageTarif entity.');
        }

        $_edit_form = $this->createEditForm($_usage_tarif);

        return $this->render('AdminBundle:CtUsageTarif:edit.html.twig', array(
            'usage' => $_usage_tarif,
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
        $_usage_tarif_manager = $this->get(ServiceName::SRV_METIER_USAGE_TARIF);

        $_usage_tarif = new CtUsageTarif();
        $_form       = $this->createCreateForm($_usage_tarif);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            $_date_time = new \DateTime("now");
            $_current_year = $_date_time->format('Y');
            $_usage_tarif->setUsgTrfAnnee($_current_year);

            // Enregistrement usage_tarif
            $_return = $_usage_tarif_manager->saveCtUsageTarif($_usage_tarif, 'new');

            if ($_return) {
                $_usage_tarif_manager->setFlash('success', "Usage Tarif ajoutée");

                return $this->redirect($this->generateUrl('usage_tarif_index'));
            }

        }

        return $this->render('AdminBundle:CtUsageTarif:add.html.twig', array(
            'usage_tarif' => $_usage_tarif,
            'form'       => $_form->createView(),
        ));
    }

    /**
     * Modification usage
     * @param Request $_request requête
     * @param CtUsageTarif $_usage_tarif
     * @return Render page
     */
    public function updateAction(Request $_request, CtUsageTarif $_usage_tarif)
    {
        // Récupérer manager
        $_usage_tarif_manager = $this->get(ServiceName::SRV_METIER_USAGE_TARIF);

        if (!$_usage_tarif) {
            throw $this->createNotFoundException('Unable to find Usage entity.');
        }

        $_edit_form = $this->createEditForm($_usage_tarif);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_usage_tarif_manager->saveCtUsageTarif($_usage_tarif, 'update');

            $_usage_tarif_manager->setFlash('success', "UsageTarif modifiée");

            return $this->redirect($this->generateUrl('usage_tarif_index'));
        }

        return $this->render('AdminBundle:CtUsageTarif:edit.html.twig', array(
            'usage' => $_usage_tarif,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition usage
     * @param CtUsageTarif $_usage_tarif The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtUsageTarif $_usage_tarif)
    {
        $_form = $this->createForm(CtUsageTarifType::class, $_usage_tarif, array(
            'action' => $this->generateUrl('usage_tarif_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création usage
     * @param CtUsageTarif $_usage_tarif_ The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtUsageTarif $_usage_tarif)
    {
        $_form = $this->createForm(CtUsageTarifType::class, $_usage_tarif, array(
            'action' => $this->generateUrl('usage_tarif_update', array('id' => $_usage_tarif->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression usage
     * @param Request $_request requête
     * @param CtUsageTarif $_usage_tarif
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtUsageTarif $_usage_tarif)
    {
        // Récupérer manager
        $_usage_tarif_manager = $this->get(ServiceName::SRV_METIER_USAGE_TARIF);

        $_form = $this->createDeleteForm($_usage_tarif);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression usage
            $_usage_tarif_manager->deleteCtUsageTarif($_usage_tarif);

            $_usage_tarif_manager->setFlash('success', 'Usage tarif supprimée');
        }

        return $this->redirectToRoute('usage_tarif_index');
    }

    /**
     * Création formulaire de suppression usage
     * @param CtUsageTarif $_usage_tarif The Usage entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtUsageTarif $_usage_tarif)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('usage_tarif_delete', array('id' => $_usage_tarif->getId())))
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
        $_usage_tarif_manager = $this->get(ServiceName::SRV_METIER_USAGE_TARIF);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_usage_tarif_manager->deleteGroupCtUsageTarif($_ids);
        }

        $_usage_tarif_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('usage_tarif_index'));
    }
}
