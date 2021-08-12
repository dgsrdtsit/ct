<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtBordereauType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtBordereau;
use Ct\Service\MetierManagerBundle\Entity\CtImprimeTechUse;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class CtBordereauController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtBordereauController extends Controller
{
    /**
     * Afficher tout les bordereau
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_bl_manager = $this->get(ServiceName::SRV_METIER_BORDEREAU);

        // Récupérer tout les imprimés tech
        $_bl = $_bl_manager->getAllCtBordereauByOrder(array('id' => 'DESC'));
        return $this->render('AdminBundle:CtBordereau:index.html.twig', array(
            'its_in_bl' => $_bl,
        ));
    }

    /**
     * Afficher tout les bordereau
     * @return Render page
     */
    public function ActivationAction()
    {
        // Récupérer manager
        $_bl_manager = $this->get(ServiceName::SRV_METIER_BORDEREAU);

        // Récupérer tout les imprimés tech
        $_bl = $_bl_manager->getCtBordereauByCentre();
        return $this->render('AdminBundle:CtBordereau:actived.html.twig', array(
            'its_in_bl' => $_bl,
        ));
    }

    /**
     *  Activer un serie d'imprimés techniques
     *  @param integer $_id
     *  @return Redirect redirection
     */
    public function ActivedAction($_id)
    {
        // Utilisateur connecté   
        $_user_connected= $this->get('security.token_storage')->getToken()->getUser();

        // Récupérer manager
        $_itu_manager   = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_bl_manager    = $this->get(ServiceName::SRV_METIER_BORDEREAU);
        $_it_in_bl      = $_bl_manager->getCtBordereauById($_id);
        $_ct_bordereau_id = $_it_in_bl;
        $_ct_centre_id  = $_it_in_bl->getCtCentre();
        $_ct_imprime_tech_id  = $_it_in_bl->getCtImprimeTech();
        $_nomImrpimeTech= $_it_in_bl->getCtImprimeTech()->getNomImprimeTech();
        $_start = $_it_in_bl->getBlDebutNumero();
        $_ending= $_it_in_bl->getBlFinNumero();
        for($i = $_start; $i <= $_ending; $i++){
            $_itu_manager->save_CtImprimeTechUse($_ct_bordereau_id, $_ct_centre_id, $_ct_imprime_tech_id, $i);
        }
        $_bl_manager->setFlash('success', 'Les '.$_nomImrpimeTech.' N° '.$_start.' à '.$_ending.' sont activés pour votre centre.');

        return $this->redirectToRoute('bordereau_activation');        
    }

    /**
     * Affichage page modification bordereau
     * @param CtBordereau $_bordereau
     * @return Render page
     */
    public function editAction(CtBordereau $_bordereau)
    {
        if (!$_bordereau) {
            throw $this->createNotFoundException('Unable to find bordereau entity.');
        }

        $_edit_form = $this->createEditForm($_bordereau);

        return $this->render('AdminBundle:Ctbordereau:edit.html.twig', array(
            'bordereau' => $_bordereau,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création bordereau
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_bl_manager= $this->get(ServiceName::SRV_METIER_BORDEREAU);

        $_bordereau = new CtBordereau();
        $_form      = $this->createCreateForm($_bordereau);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement bordreau
            $_bl_manager->saveCtBordereau($_bordereau, 'new');
            $_bl_manager->setFlash('success', "Imprimé technique ajouté dans bordereau");
            return $this->redirect($this->generateUrl('bordereau_index'));
        }

        return $this->render('AdminBundle:CtBordereau:add.html.twig', array(
            'bordereau' => $_bordereau,
            'form'       => $_form->createView()
        ));
    }

    /**
     * Modification bordereau
     * @param Request $_request requête
     * @param CtImprimeTech $_bordereau
     * @return Render page
     */
    public function updateAction(Request $_request, CtBordereau $_bordereau)
    {
        // Récupérer manager
        $_bl_manager = $this->get(ServiceName::SRV_METIER_BORDEREAU);

        if (!$_bl_manager) {
            throw $this->createNotFoundException('Unable to find bordereau entity.');
        }

        $_edit_form = $this->createEditForm($_bordereau);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_bl_manager->saveCtBordereau($_bordereau, 'update');
            $_bl_manager->setFlash('success', "Imprimé technique modifié dans bordereau");
            return $this->redirect($this->generateUrl('bordereau_index'));
        }

        return $this->render('AdminBundle:CtBordereau:edit.html.twig', array(
            'bordereau' => $_bordereau,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition bordereau
     * @param CtBordereau $_bordereau The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtBordereau $_bordereau)
    {
        $_form = $this->createForm(CtBordereauType::class, $_bordereau, array(
            'action' => $this->generateUrl('bordereau_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création bordereau
     * @param CtBordereau $_bordereau The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtBordereau $_bordereau)
    {
        $_form = $this->createForm(CtBordereauType::class, $_bordereau, array(
            'action' => $this->generateUrl('bordereau_update', array('id' => $_bordereau->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression d'un imprimé technique dans un bordreau
     * @param Request $_request requête
     * @param CtBordereau $_bordereau
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtBordereau $_bordereau)
    {
        // Récupérer manager
        $_bl_manager = $this->get(ServiceName::SRV_METIER_BORDEREAU);

        $_form = $this->createDeleteForm($_bordereau);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_bl_manager->deleteCtBordereau($_bordereau);

            $_bl_manager->setFlash('success', 'Imprimé technique supprimé du bordereau.');
        }

        return $this->redirectToRoute('bordreau_index');
    }

    /**
     * Création formulaire de suppression imprimé tech du bordereau
     * @param CtBordereau $_bordereau The imprimé tech entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtBordereau $_bordereau)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bordereau_delete', array('id' => $_bordereau->getId())))
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
        $_bl_manager = $this->get(ServiceName::SRV_METIER_BORDEREAU);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_bl_manager->deleteGroupCtBordereau($_ids);
        }

        $_bl_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('bordereau_index'));
    }

    /**
     * Création formulaire de suppression imprimé tech du bordereau
     * @param CtBordereau $_bordereau The imprimé tech entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createSearchForm(CtBordereau $_bordereau)
    {
        $_form = $this->createForm(CtBordereauType::class, $_bordereau, array(
            'action' => $this->generateUrl('bordereau_search', array()),
            'method' => 'POST',
        ));

        return $_form;
    }

    /**
     * Recherche bordereau pour un centre
     * @param Request $_request requête
     * @return Render page
     */
    public function searchAction(Request $_request)
    {
        $_bl_manager = $this->get(ServiceName::SRV_METIER_BORDEREAU);
        $_province_manager  = $this->get(ServiceName::SRV_METIER_PROVINCE);
        $_ctr_manager = $this->get(ServiceName::SRV_METIER_CENTRE);
        // Chargement de formulaire
        $_bordereau = new CtBordereau();
        $_search_form = $this->createSearchForm($_bordereau);
        $_search_form->handleRequest($_request);
        // Initilisation des listes
        $_list_in_its = "";
        $bl_numero = "";
        $_ct_centre_id = "";
        $_ctr_nom = "";
        // Charger la liste des centres et provinces
        $_provinces = $_province_manager->getAllCtProvinceByOrder(array('id' => 'ASC'));
        $_list_centres = $_ctr_manager->getAllCtCentreByOrder(array('ctrNom' => 'ASC'));

        if ($_request->getMethod() === 'POST' ) {
            // Récupération données formulaires
            $_data_forms   = $_request->request->all();
            $bl_numero = isset($_data_forms['bl_numero']) ? $_data_forms['bl_numero'] : '';
            $ct_centre_id = isset($_data_forms['ct_centre_id']) ? $_data_forms['ct_centre_id'] : '';
            // Recherche du centre selectionné
            $_centre = $_ctr_manager->getCtCentreById($ct_centre_id);
            $_ct_centre_id = $_centre->getId();
            $_ctr_nom = $_centre->getCtrNom();
            // Recherche par numéro de bordereau et ID centre de destinataire
            $_list_in_its = $_bl_manager->getListInBordereau($ct_centre_id, $bl_numero);
            if(count($_list_in_its) == 0) $_bl_manager->setFlash('error', 'Le bordereau N° '.$bl_numero.' pour le centre '.$_ctr_nom.' est introuvable.');
        }

        return $this->render('AdminBundle:CtBordereau:search.html.twig', array(
            'list_in_its' => $_list_in_its,
            'bl_numero' => $bl_numero,
            'ct_centre_id' => $_ct_centre_id,
            'ctr_nom' => $_ctr_nom,
            'list_provinces' => $_provinces,
            'list_centres' => $_list_centres,
            'search_form' => $_search_form->createView()
        ));
    }

    /**
     * Generer bordereau de livraison
     * @param Request $_request
     * @return Render page
     */
    public function genererBordereauLivraisonAction(Request $_request)
    {
        // Récupérer manager
        $_bordereau_manager = $this->get(ServiceName::SRV_METIER_BORDEREAU);

        // Récupération données formulaires
        $_data_forms    = $_request->request->all();
        $_ct_centre_id  = array_key_exists('ct_centre_id', $_data_forms) ? $_data_forms['ct_centre_id'] : 0;
        $_bl_numero     = array_key_exists('bl_numero', $_data_forms) ? $_data_forms['bl_numero'] : 0;

        // Formattage du bordereau de livraison
        $_link_download = $_bordereau_manager->generateBordereauLivraison($_ct_centre_id, $_bl_numero);

        return new JsonResponse($_link_download);
    }
}
