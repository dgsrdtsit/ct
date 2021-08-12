<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtVehicule;
use Ct\Service\MetierManagerBundle\Form\CtVehiculeType;
use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtCarteGriseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtCarteGrise;

/**
 * Class CtCarteGriseController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtCarteGriseController extends Controller
{
    /**
     * Afficher tout les carte grise
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_carte_grise_manager = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);

        $_is_super_admin = $_reception_manager->isSuperAdmin();
        $_centre_id = $_reception_manager->isChefDeCentre();
        if ($_is_super_admin === false && $_centre_id === 0) {
            throw $this->createAccessDeniedException('You cannot access this page!');
        } else {
            // Récupérer tout les carte grise
            $_carte_grises = $_carte_grise_manager->getAllCtCarteGriseByOrder(array('id' => 'DESC'));

            return $this->render('AdminBundle:CtCarteGrise:index.html.twig', array(
                'carte_grises' => $_carte_grises,
            ));
        }


    }

    /**
     * Affichage page modification carte grise
     * @param CtCarteGrise $_carte_grise
     * @return Render page
     */
    public function editAction(CtCarteGrise $_carte_grise)
    {
        if (!$_carte_grise) {
            throw $this->createNotFoundException('Unable to find CtCarteGrise entity.');
        }

        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);

        $_success_carte_grise_id = $this->get('session')->getFlashBag()->get('success_carte_grise_id');

        if (isset($_success_carte_grise_id) && count($_success_carte_grise_id) > 0 &&
            $_success_carte_grise_id[0] === $_carte_grise->getId()) { // Show edit after new insertion
            $_show_edit_page = true;
        } else {
            $_is_super_admin = $_reception_manager->isSuperAdmin();
            $_centre_id = $_reception_manager->isChefDeCentre();
            if (!$_is_super_admin && !$_centre_id) { // Not SUPER_ADMIN and Not ADMIN
                $_show_edit_page = true;
            } elseif ($_centre_id) {
                if ($_centre_id !== $_carte_grise->getCtCentre()->getId()) { // ADMIN cannot edit Visite from another Centre
                    $_show_edit_page = false;
                } else {
                    $_show_edit_page = true;
                }
            } else { // SUPER_ADMIN can do all edit
                $_show_edit_page = true;
            }
        }
        if ($_show_edit_page === false) {
            throw $this->createAccessDeniedException('You cannot access this page!');
        }

        $_edit_form = $this->createEditForm($_carte_grise);

        return $this->render('AdminBundle:CtCarteGrise:edit.html.twig', array(
            'carte_grise' => $_carte_grise,
            'edit_form'   => $_edit_form->createView()
        ));
    }

    /**
     * Création carte grise
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_carte_grise_manager = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);

        // Récupérer le centre de l'utilisateur connecté
        $_user_connected = $this->get('security.token_storage')->getToken()->getUser();

        $_carte_grise = new CtCarteGrise();
        $_form        = $this->createCreateForm($_carte_grise);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Tester l'existence numéro d'immatriculation
            $_numero_immatriculation       = $_form['cgImmatriculation']->getData();
            $_numero_immatriculation_exist = $_carte_grise_manager->findByImmatriculation($_numero_immatriculation);
            if ($_numero_immatriculation_exist) {
                $_message = "Numéro d'immatriculation déjà pris !";
                $_carte_grise_manager->setFlash('error', $_message);

                return $this->redirect($this->generateUrl('carte_grise_new'));
            }

            // Si le rôle est administrateur, on récupère leur centre
            $_role_user = $_user_connected->getCtRole()->getId();
            if ($_role_user == RoleName::ID_ROLE_ADMIN) {
                $_centre_admin = $_user_connected->getCtCentre();
                $_carte_grise->setCtCentre($_centre_admin);
            }

            // Enregistrement carte grise
            $_carte_grise_manager->addCtCarteGrise($_carte_grise, 'new');

            $_carte_grise_manager->setFlash('success', "Carte grise ajoutée");
            $this->get('session')->getFlashBag()->set('success_carte_grise_id', $_carte_grise->getId()); // For security purpose

            return $this->redirect($this->generateUrl('carte_grise_new'));
        }

        return $this->render('AdminBundle:CtCarteGrise:add.html.twig', array(
            'carte_grise' => $_carte_grise,
            'form'        => $_form->createView()
        ));
    }

    /**
     * Modification carte grise
     * @param Request $_request requête
     * @param CtCarteGrise $_carte_grise
     * @return Render page
     */
    public function updateAction(Request $_request, CtCarteGrise $_carte_grise)
    {
        // Récupérer manager
        $_carte_grise_manager = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);

        if (!$_carte_grise) {
            throw $this->createNotFoundException('Unable to find CtCarteGrise entity.');
        }

        $_edit_form = $this->createEditForm($_carte_grise);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_carte_grise_manager->updateCtCarteGrise($_carte_grise);

            $_carte_grise_manager->setFlash('success', "Carte grise modifiée");
            $this->get('session')->getFlashBag()->set('success_carte_grise_id', $_carte_grise->getId()); // For security purpose

            return $this->redirect($this->generateUrl('carte_grise_edit', array('id' => $_carte_grise->getId())));
        }

        return $this->render('AdminBundle:CtCarteGrise:edit.html.twig', array(
            'carte_grise' => $_carte_grise,
            'edit_form'   => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition carte grise
     * @param CtCarteGrise $_carte_grise The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtCarteGrise $_carte_grise)
    {
        $_form = $this->createForm(CtCarteGriseType::class, $_carte_grise, array(
            'action' => $this->generateUrl('carte_grise_new'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création carte grise
     * @param CtCarteGrise $_carte_grise The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtCarteGrise $_carte_grise)
    {
        $_form = $this->createForm(CtCarteGriseType::class, $_carte_grise, array(
            'action' => $this->generateUrl('carte_grise_update', array('id' => $_carte_grise->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression carte grise
     * @param Request $_request requête
     * @param CtCarteGrise $_carte_grise
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtCarteGrise $_carte_grise)
    {
        // Récupérer manager
        $_carte_grise_manager = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);

        $_form = $this->createDeleteForm($_carte_grise);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carte grise
            $_carte_grise_manager->deleteCtCarteGrise($_carte_grise);

            $_carte_grise_manager->setFlash('success', 'Carte grise supprimée');
        }

        return $this->redirectToRoute('carte_grise_index');
    }

    /**
     * Création formulaire de suppression carte grise
     * @param CtCarteGrise $_carte_grise The CtCarteGrise entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtCarteGrise $_carte_grise)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('carte_grise_delete', array('id' => $_carte_grise->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste carte grise
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_carte_grise_manager = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_carte_grise_manager->deleteGroupCtCarteGrise($_ids);
        }

        $_carte_grise_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('carte_grise_index'));
    }

    /**
     * Préremplir les formulaires lors de recherche
     * @param Request $_request requête
     * @return Render page
     */
    public function fillFormAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_carte_grise_manager = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);

        // Récupération données formulaires
        $_data_forms             = $_request->request->all();
        $_numero_immatriculation = $_data_forms['numero_immatriculation'];

        $_carte_grise = $_carte_grise_manager->findByImmatriculation($_numero_immatriculation);

        $_statut           = array();
        $_statut['statut'] = false;
        if ($_carte_grise) {
            $_form = $this->createCreateForm($_carte_grise[0]);
            $_form->handleRequest($_request);

            $_statut['statut']  = true;
            $_statut['message'] = "Numéro d'immatriculation est déjà existe !";
            $_statut['view']   = $this->renderView('AdminBundle:CtCarteGrise:partial_add.html.twig', array(
                'carte_grise' => $_carte_grise[0],
                'form'        => $_form->createView()
            ));
        }

        return new JsonResponse($_statut);
    }

    /**
     * Préremplir les formulaires lors de recherche par numéro d'identification
     * @param Request $_request requête
     * @return Render page
     */
    public function fillFormAjaxByNumeroIdentificationAction(Request $_request)
    {
        // Récupérer manager
        $_carte_grise_manager = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);

        // Récupération données formulaires
        $_data_forms            = $_request->request->all();
        $_numero_identification = $_data_forms['numero_identification'];

        $_carte_grise = $_carte_grise_manager->findByNumeroIdentification($_numero_identification);

        $_statut           = array();
        $_statut['statut'] = false;
        if ($_carte_grise) {
            $_form = $this->createCreateForm($_carte_grise[0]);
            $_form->handleRequest($_request);

            $_statut['statut']  = true;
            $_statut['message'] = "Numéro d'identification est déjà existe !";
            $_statut['view']   = $this->renderView('AdminBundle:CtCarteGrise:partial_add.html.twig', array(
                'carte_grise' => $_carte_grise[0],
                'form'        => $_form->createView()
            ));
        }

        return new JsonResponse($_statut);
    }

    /**
     * Autocomplète pour le numéro d'immatriculation
     * @param Request $_request
     * @return JsonResponse
     */
    public function showNumeroImmatriculationAjaxAction(Request $_request) {
        // Récupérer manager
        $_carte_grise_manager = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);

        // Récupérer les données formulaire
        $_data = $_request->request->all();
        $_term = $_data['term'];

        // Récupérer les listes numéro d'immatriculation
        $_list_numero_immatriculation = $_carte_grise_manager->getListNumeroImmatriculation($_term);

        return $_list_numero_immatriculation;
    }

    /**
     * Autocomplète pour le numéro d'identification
     * @param Request $_request
     * @return JsonResponse
     */
    public function showNumeroIdentificationAjaxAction(Request $_request) {
        // Récupérer manager
        $_carte_grise_manager = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);

        // Récupérer les données formulaire
        $_data = $_request->request->all();
        $_term = $_data['term'];

        // Récupérer les listes numéro d'identification
        $_list_numero_identification = $_carte_grise_manager->getListNumeroImmatriculation($_term);

        return $_list_numero_identification;
    }

    /**
     * Ajax pour vérifier l'existence carte grise
     * @param Request $_request
     * @return JsonResponse
     */
    public function checkCarteGriseAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_vehicule_manager    = $this->get(ServiceName::SRV_METIER_VEHICULE);
        $_carte_grise_manager = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);

        // Récupérer les données formulaire
        $_data         = $_request->request->all();
        $_numero_serie = $_data['numero_serie'];

        // Tester si le numéro de série a déjà un carte grise
        $_carte_grise = $_carte_grise_manager->getCarteGriseByNumeroSerie($_numero_serie);

        $_statut           = array();
        $_statut['statut'] = false;
        if ($_carte_grise) {
            $_form = $this->createCreateForm($_carte_grise[0]);
            $_form->handleRequest($_request);

            $_statut['statut']  = true;
            $_statut['type']    = 'carte-grise';
            $_statut['message'] = "Numéro d'immatriculation est déjà existe !";
            $_statut['view']   = $this->renderView('AdminBundle:CtCarteGrise:partial_add.html.twig', array(
                'carte_grise' => $_carte_grise[0],
                'form'        => $_form->createView()
            ));

            return new JsonResponse($_statut);
        }

        // Tester si le numéro de série existe déjà
        $_numero_serie_exist = $_vehicule_manager->findByNumeroSerie($_numero_serie);
        if ($_numero_serie_exist) {
            $_vehicule = $_vehicule_manager->getVehiculeInfoFromReception($_numero_serie);

            $_statut['statut']   = true;
            $_statut['type']     = 'numero-serie';
            $_statut['message']  = "Numéro de série est déjà existe et qui n'a pas encore de numéro d'immatriculation !";
            $_statut['vehicule'] = $_vehicule;
        }

        return new JsonResponse($_statut);
    }

    /**
     * Autocomplète pour le numéro de série vehicule
     * @param Request $_request
     * @return JsonResponse
     */
    public function showNumeroSerieAjaxAction(Request $_request) {
        // Récupérer manager
        $_carte_grise_manager = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);

        // Récupérer les données formulaire
        $_data = $_request->request->all();
        $_term = $_data['term'];

        // Récupérer les listes numéro de série
        $_list_numero_serie = $_carte_grise_manager->getListNumeroSerie($_term);

        return $_list_numero_serie;
    }

    /**
     * Ajax pour compléter le formulaire carte grise par numéro de série
     * @param Request $_request
     * @return JsonResponse
     */
    public function fillInfoByImmatriculationAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_carte_grise_manager = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);

        // Récupérer les données formulaire
        $_data   = $_request->request->all();
        $_numero = $_data['numero'];
        $_type   = $_data['type'];

        // Récupérer les informations carte grise
        $_carte_grise = $_carte_grise_manager->getArrayInfoCarteGriseByNumero($_numero, $_type);

        $_statut           = array();
        $_statut['statut'] = false;
        if ($_carte_grise) {
            $_statut['statut']  = true;
            $_statut['message'] = 'Recherche carte grise par numéro ' . $_type . ' trouvé !';
            $_statut['data']    = $_carte_grise;
        }

        return new JsonResponse($_statut);
    }

    /**
     * Ajax pour compléter le formulaire carte grise par numéro d'identification
     * @param Request $_request
     * @return JsonResponse
     */
    public function fillInfoByNumeroIdentificationAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_carte_grise_manager = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);

        // Récupérer les données formulaire
        $_data   = $_request->request->all();
        $_numero = $_data['numero'];
        $_type   = $_data['type'];

        // Récupérer les informations carte grise
        $_carte_grise = $_carte_grise_manager->getArrayInfoCarteGriseByNumero($_numero, $_type);

        $_statut           = array();
        $_statut['statut'] = false;
        if ($_carte_grise) {
            $_statut['statut']  = true;
            $_statut['message'] = 'Recherche carte grise par numéro ' . $_type . ' trouvé !';
            $_statut['data']    = $_carte_grise;
        }

        return new JsonResponse($_statut);
    }
}
