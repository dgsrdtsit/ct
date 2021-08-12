<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtVisiteType;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtVisite;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CtVisiteController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtVisiteController extends Controller
{
    /**
     * Afficher tout les visite
     * @return Render page
     */
    public function indexAction()
    {

        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        $_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);

        $_is_super_admin = $_reception_manager->isSuperAdmin();
        $_centre_id = $_reception_manager->isChefDeCentre();
        if ($_is_super_admin === false && $_centre_id === 0) {
            throw $this->createAccessDeniedException('You cannot access this page!');
        } else {
            $_visites = $_visite_manager->getAllCtVisiteByCentreOrder(array('id' => 'DESC'), $_centre_id);
            return $this->render('AdminBundle:CtVisite:index.html.twig', array(
                'visites' => $_visites
            ));
        }

    }

    /**
     * Affichage page modification visite
     * @param CtVisite $_visite
     * @return Render page
     */
    public function editAction(CtVisite $_visite)
    {
        if (!$_visite) {
            throw $this->createNotFoundException('Unable to find CtVisite entity.');
        }

        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        /* ============================== Liste des imprimés tech par type ============================== */
        $_imprime_tech = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);
        $_imprime_tech_use = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimestech = $_imprime_tech->getAllCtImprimeTechByOrder(array('nomImprimeTech' => 'ASC'));
        $_imprimestechuse = $_imprime_tech_use->getAllCtImprimeTechNoUsedOrder();
        /* ============================================================================================== */

        $_success_visite_id = $this->get('session')->getFlashBag()->get('success_visite_id');

        if (isset($_success_visite_id) && count($_success_visite_id) > 0 &&
            $_success_visite_id[0] === $_visite->getId()) { // Show edit after new insertion
            $_show_edit_page = true;
        } else {
            $_is_super_admin = $_reception_manager->isSuperAdmin();
            $_centre_id = $_reception_manager->isChefDeCentre();
            if (!$_is_super_admin && !$_centre_id) { // Not SUPER_ADMIN and Not ADMIN
                $_show_edit_page = true;
            } elseif ($_centre_id) {
                if ($_centre_id !== $_visite->getCtCentre()->getId()) { // ADMIN cannot edit Visite from another Centre
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

        /* ====================== Récupération data imprimés techniques utilisés ====================== */
        $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimes_use = $_em_imprimes->getCtImprimeTechUseByCtControle($_visite->getId());
        /* ============================================================================================ */

        $_edit_form = $this->createEditForm($_visite);

        return $this->render('AdminBundle:CtVisite:edit.html.twig', array(
            'visite'    => $_visite,
            'imprimes_use'  => $_imprimes_use,
            'imprimes_tech'  => $_imprimestech,
            'imprimes_tech_use' => $_imprimestechuse,
            'edit_form' => $_edit_form->createView()
        ));
    }

    /**
     * Création visite
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_visite_manager    = $this->get(ServiceName::SRV_METIER_VISITE);
        $_province_manager  = $this->get(ServiceName::SRV_METIER_PROVINCE);
        $_centre_manager    = $this->get(ServiceName::SRV_METIER_CENTRE);
        $_verif_manager     = $this->get(ServiceName::SRV_METIER_USER);
        $_visite_type_manager = $this->get(ServiceName::SRV_METIER_TYPE_VISITE);

        $_user_connected    = $this->container->get('security.token_storage')->getToken()->getUser();
        $_role_user         = $_user_connected->getCtRole()->getId();
        if ($_role_user != RoleName::ID_ROLE_SUPERADMIN) {
            $_centre_id = $_user_connected->getCtCentre()->getId();
            $_verificateurs = $_verif_manager->getListVerificateurByCentre($_centre_id, 1);
        } else {
            $_verificateurs = $_verif_manager->getListVerificateurByCentre(0, 1);
        }
        // Récupérer tout les provinces et les centres
        $_centres   = $_centre_manager->getAllCtCentreByOrder(array('id' => 'ASC'));
        $_provinces = $_province_manager->getAllCtProvinceByOrder(array('id' => 'ASC'));
        // Récupérer tout les imprimés techniques
        $_imprime_tech = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);
        $_imprime_tech_use = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimestech = $_imprime_tech->getAllCtImprimeTechByOrder(array('nomImprimeTech' => 'ASC'));
        $_imprimestechuse = $_imprime_tech_use->getAllCtImprimeTechNoUsedOrder();

        $_visite_type = $_visite_type_manager->getAllCtTypeVisite();

        $_visite = new CtVisite();
        $_form   = $this->createCreateForm($_visite);
        $_form->handleRequest($_request);

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Vérifier si la voiture a déjà fait un visite dans la même journée
            $_carte_grise     = $_visite->getCtCarteGrise()->getCgImmatriculation();
            $_is_visite_exist = $_visite_manager->isVisiteExist($_carte_grise);
            if ($_is_visite_exist) {
                $_message = "Visite technique a été déjà fait dans la journée";
                $_statut  = "error";

                $_visite_manager->setFlash($_statut, $_message);
                $this->get('session')->getFlashBag()->set('success_visite_id', $_visite->getId());

                return $this->redirect($this->generateUrl('visite_new'));
            } else {
                // Enregistrement visite
                $_visite_manager->addCtVisite($_visite, 'new');

                /* ============ Misa à jour des imprimés utilisés pour cette visite ============ */
                $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
                $_data = $_request->request->all();
                $_list_itu = $_data['ct_imprime_tech_use']; $x = NULL;
                foreach($_list_itu as $_one_uti){
                    $_imprime_tech_use = $_em_imprimes->getCtImprimeTechUseById($_one_uti);
                    $_em_imprimes->saveCtImprimeTechUse($_imprime_tech_use, 'Visite', $_visite->getId());
                }
                /* ============================================================================== */

                $_message = "Visite technique ajouté";
                $_statut  = "success";

                $_visite_manager->setFlash($_statut, $_message);
                $this->get('session')->getFlashBag()->set('success_visite_id', $_visite->getId());

                return $this->redirect($this->generateUrl('visite_edit', array('id' => $_visite->getId())));
            }
        }

        return $this->render('AdminBundle:CtVisite:add.html.twig', array(
            'type_visites'  => $_visite_type,
            'visite'        => $_visite,
            'form'          => $_form->createView(),
            'centres'       => $_centres,
            'provinces'     => $_provinces,
            'verificateurs' => $_verificateurs,
            'imprimes_tech'  => $_imprimestech,
            'imprimes_tech_use' => $_imprimestechuse,
        ));
    }

    /**
     * Modification visite
     * @param Request $_request requête
     * @param CtVisite $_visite
     * @return Render page
     */
    public function updateAction(Request $_request, CtVisite $_visite)
    {
        // Récupérer manager
        $_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);
        // Récupérer tout les imprimés techniques
        $_imprime_tech = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);
        $_imprime_tech_use = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimestech = $_imprime_tech->getAllCtImprimeTechByOrder(array('nomImprimeTech' => 'ASC'));
        $_imprimestechuse = $_imprime_tech_use->getAllCtImprimeTechNoUsedOrder();

        if (!$_visite) {
            throw $this->createNotFoundException('Unable to find CtVisite entity.');
        }

        $_edit_form = $this->createEditForm($_visite);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_visite_manager->updateCtVisite($_visite);

            /* ============ Misa à jour des imprimés utilisés pour cette visite ============ */
            $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
            $_data = $_request->request->all();
            $_list_itu = $_data['ct_imprime_tech_use']; $x = NULL;
            foreach($_list_itu as $_one_uti){
                $_imprime_tech_use = $_em_imprimes->getCtImprimeTechUseById($_one_uti);
                $_em_imprimes->saveCtImprimeTechUse($_imprime_tech_use, 'Visite', $_visite->getId());
            }
            /* ============================================================================== */

            $_visite_manager->setFlash('success', "Visite technique modifié");

            $this->get('session')->getFlashBag()->set('success_visite_id', $_visite->getId()); // For security purpose
            return $this->redirect($this->generateUrl('visite_edit', array('id' => $_visite->getId())));
        }

        return $this->render('AdminBundle:CtVisite:edit.html.twig', array(
            'visite'    => $_visite,
            'imprimes_tech'  => $_imprimestech,
            'imprimes_tech_use' => $_imprimestechuse,
            'edit_form' => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition visite
     * @param CtVisite $_visite The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtVisite $_visite)
    {
        // Récupérer le centre de l'utilisateur connecté
        $_centre         = '';
        $_user_connected = $this->container->get('security.token_storage')->getToken()->getUser();
        $_role_user      = $_user_connected->getCtRole()->getId();
        if ($_role_user != RoleName::ID_ROLE_SUPERADMIN)
            $_centre = $_user_connected->getCtCentre()->getId();

        $_form = $this->createForm(CtVisiteType::class, $_visite, array(
            'action' => $this->generateUrl('visite_new'),
            'method' => 'POST',
            'centre' => $_centre
        ));

        return $_form;
    }

    /**
     * Création formulaire de création visite
     * @param CtVisite $_visite The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtVisite $_visite)
    {
        // Récupérer le centre de l'utilisateur connecté
        $_centre         = '';
        $_user_connected = $this->container->get('security.token_storage')->getToken()->getUser();
        $_role_user      = $_user_connected->getCtRole()->getId();
        if ($_role_user != RoleName::ID_ROLE_SUPERADMIN)
            $_centre = $_user_connected->getCtCentre()->getId();

        $_form = $this->createForm(CtVisiteType::class, $_visite, array(
            'action' => $this->generateUrl('visite_update', array('id' => $_visite->getId())),
            'method' => 'PUT',
            'centre' => $_centre
        ));

        return $_form;
    }

    /**
     * Suppression visite
     * @param Request $_request requête
     * @param CtVisite $_visite
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtVisite $_visite)
    {
        // Récupérer manager
        $_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);

        $_form = $this->createDeleteForm($_visite);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression visite
            $_visite_manager->deleteCtVisite($_visite);

            $_visite_manager->setFlash('success', 'Visite technique supprimé');
        }

        return $this->redirectToRoute('visite_index');
    }

    /**
     * Création formulaire de suppression visite
     * @param CtVisite $_visite The CtVisite entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtVisite $_visite)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('visite_delete', array('id' => $_visite->getId())))
                    ->setMethod('DELETE')
                    ->getForm();
    }

    /**
     * Suppression par groupe séléctionnée
     * @param Request $_request
     * @return Redirect liste visite
     */
    public function deleteGroupAction(Request $_request)
    {
        // Récupérer manager
        $_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_visite_manager->deleteGroupCtVisite($_ids);
        }

        $_visite_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('visite_index'));
    }

    /**
     * Ajax génération reporting carte visite
     * @param Request $_request
     * @return JsonResponse
     */
    public function generateReportingAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);

        // Récupération données formulaires
        $_data_forms = $_request->request->all();
        $_id_visite  = $_data_forms['id'];

        $_visite = $_visite_manager->getCtVisiteById($_id_visite);

        $_link_download = $_visite_manager->generateReporting($_visite);

        return new JsonResponse($_link_download);
    }

    /**
     * Ajax génération reporting feuille de caisse
     * @param Request $_request
     * @return Render page
     */
    public function generateReportingFeuilleCaisseAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);

        // Récupération données formulaires
        $_data_forms    = $_request->request->all();
        $_date          = array_key_exists('date', $_data_forms) ? $_data_forms['date'] : date('d/m/Y');
        $_centre_id     = array_key_exists('centre_id', $_data_forms) ? $_data_forms['centre_id'] : 0;
        $_type_visite_id= array_key_exists('type_visite', $_data_forms) ? $_data_forms['type_visite'] : 0;

        // Formattage date
        $_date_exploded = explode('/', $_date);
        $_date_parsed   = $_date_exploded[2] . '-' . $_date_exploded[1] . '-' . $_date_exploded[0];

        $_link_download = $_visite_manager->generateFeuilleCaisse($_date_parsed, $_centre_id, $_type_visite_id);

        return new JsonResponse($_link_download);
    }

    /**
     * Ajax génération reporting fiche de contrôle
     * @param Request $_request
     * @return JsonResponse
     */
    public function generateReportingFicheControleAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);

        // Récupération données formulaires
        $_data_forms = $_request->request->all();
        $_date       = array_key_exists('date', $_data_forms) ? $_data_forms['date'] : date('d/m/Y');
        $_centre_id  = array_key_exists('centre_id', $_data_forms) ? $_data_forms['centre_id'] : 0;
        $_verif_id   = array_key_exists('verif_id', $_data_forms) ? $_data_forms['verif_id'] : 0;

        // Formattage date
        $_date_exploded = explode('/', $_date);
        $_date_parsed   = $_date_exploded[2] . '-' . $_date_exploded[1] . '-' . $_date_exploded[0];

        $_link_download = $_visite_manager->generateFicheVerificateurByCentre($_centre_id, $_verif_id, $_date_parsed);

        return new JsonResponse($_link_download);
    }

    /**
     * Ajax génération reporting fiche anomalie
     * @param Request $_request
     * @return Render page
     */
    public function generateReportingFicheAnomalieAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);

        // Récupération données formulaires
        $_data_forms = $_request->request->all();
        $_date       = array_key_exists('date', $_data_forms) ? $_data_forms['date'] : date('d/m/Y');
        $_centre_id  = array_key_exists('centre_id', $_data_forms) ? $_data_forms['centre_id'] : 0;

        // Formattage date
        $_date_exploded = explode('/', $_date);
        $_date_parsed   = $_date_exploded[2] . '-' . $_date_exploded[1] . '-' . $_date_exploded[0];

        $_link_download = $_visite_manager->generateFicheAnomalieByCentre($_centre_id, $_date_parsed);

        return new JsonResponse($_link_download);
    }

    /**
     * Ajax génération PV visite
     * @param Request $_request
     * @return JsonResponse
     */
    public function generatePvVisiteAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);

        // Récupération données formulaires
        $_data_forms = $_request->request->all();
        $_id_visite  = $_data_forms['id'];

        $_link_download = $_visite_manager->generatePVVisite($_id_visite);
        //var_dump($_link_download);die;

//        $_link_download = $_visite_manager->generateReporting($_visite);

        return new JsonResponse($_link_download);
    }

    /**
     * Traitement ajax select verificateur par centre
     * @param Request $_request requête
     * @return Render JsonResponse
     */
    public function selectVerifByCentreAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_verif_manager     = $this->get(ServiceName::SRV_METIER_USER);
        $_response = new JsonResponse();

        $_verif_array = array();
        $_data = $_request->request->all();
        $_centre_id = array_key_exists('centre_id', $_data) ? $_data['centre_id'] : 0;

        if($_centre_id > 0){
            $_verificateurs = $_verif_manager->getListVerificateurByCentre($_centre_id, 1);
            foreach ($_verificateurs as $_verificateur) {
                $_verif             =  new \stdClass();
                $_verif->id         = $_verificateur->getId();
                $_verif->libelle    = $_verificateur->getUsrName();
                $_verif_array[]     = $_verif;
            }
            $_response->setData($_verif_array);
        } else {
            $_err = array("message" => "Erreur province");
            $_response->setData($_err);
        }

        return $_response;
    }

    /**
     * Traitement ajax select usage par type visite
     * @param Request $_request requête
     * @return Render JsonResponse
     */
    public function selectUsageByTypeVisiteAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_usage_manager     = $this->get(ServiceName::SRV_METIER_USAGE);
        $_response = new JsonResponse();

        $_us_array = array();
        $_data = $_request->request->all();
        $_type_visite_id = array_key_exists('type_visite_id', $_data) ? $_data['type_visite_id'] : 0;

        if($_type_visite_id > 0){
            $_usages = $_usage_manager->getCtUsageByTypeVisite($_type_visite_id);
            foreach ($_usages as $_usage) {
                $_us             =  new \stdClass();
                $_us->id         = $_usage->getId();
                $_us->libelle    = $_usage->getUsgLibelle();
                $_us_array[]     = $_us;
            }
            $_response->setData($_us_array);
        } else {
            $_err = array("message" => "Erreur province");
            $_response->setData($_err);
        }

        return $_response;
    }

    /**
     * Traitement ajax select date expiration par usage
     * @param Request $_request requête
     * @return Render JsonResponse
     */
    public function selectExpirationByUsageAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_usage_manager     = $this->get(ServiceName::SRV_METIER_USAGE);
        $_response = new JsonResponse();

        $_data = $_request->request->all();
        $_usage_id = array_key_exists('usage_id', $_data) ? $_data['usage_id'] : 0;

        if($_usage_id > 0){
            $_usage = $_usage_manager->getCtUsageById($_usage_id);
            $_validite = $_usage->getUsgValidite();
            $_expiration = date('d/m/Y', strtotime("+$_validite months", time()));
            $_response->setData(array('expiration' => $_expiration));
        } else {
            $_err = array("message" => "Erreur province");
            $_response->setData($_err);
        }

        return $_response;
    }

    /**
     * Recherche visite
     * @param Request $_request requête
     * @return Render page
     */
    public function searchAction(Request $_request)
    {
        if ($_request->isMethod('POST')) {
            // Récupération données formulaires
            $_data_forms = $_request->request->all();
            $_id_visite  = $_data_forms['id'];

            $_url_visite_edit = $this->generateUrl('visite_edit', array('id' => $_id_visite), UrlGeneratorInterface::ABSOLUTE_URL);

            return new Response($_url_visite_edit);
        }

        return $this->render('AdminBundle:CtVisite:search.html.twig');
    }
}
