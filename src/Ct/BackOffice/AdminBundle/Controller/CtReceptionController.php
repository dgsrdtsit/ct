<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtReception;
use Ct\Service\MetierManagerBundle\Entity\CtVehicule;
use Ct\Service\MetierManagerBundle\Utils\PathReportingName;
use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtReceptionType;
use Ct\Service\UserBundle\Entity\User;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class CtReceptionController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtReceptionController extends Controller
{
    /**
     * Afficher tout les receptions
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);

        $_is_super_admin = $_reception_manager->isSuperAdmin();
        $_centre_id = $_reception_manager->isChefDeCentre();
        if ($_is_super_admin === false && $_centre_id === 0) {
            throw $this->createAccessDeniedException('You cannot access this page!');
        } else {
            $_rec = $_reception_manager->getAllCtReceptionGroupedByNumGroupAndCentre(array('id' => 'ASC'), $_centre_id);
            return $this->render('AdminBundle:CtReception:index.html.twig', array(
                'receptions' => $_rec,
            ));
        }
    }

    /**
     * Affichage page modification reception
     * @param CtReception $_reception
     * @return Render page
     */
    public function editAction(CtReception $_reception)
    {
        if (!$_reception) {
            throw $this->createNotFoundException('Unable to find type reception entity.');
        }
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);

        // Récupérer tout les imprimés techniques
        $_imprime_tech = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);
        $_imprime_tech_use = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimestech = $_imprime_tech->getAllCtImprimeTechByOrder(array('nomImprimeTech' => 'ASC'));
        $_imprimestechuse = $_imprime_tech_use->getAllCtImprimeTechNoUsedOrder();

        $_success_reception_id = $this->get('session')->getFlashBag()->get('success_reception_id');

        if (isset($_success_reception_id) && count($_success_reception_id) > 0 &&
            $_success_reception_id[0] === $_reception->getId()) { // Show edit after new insertion
            $_show_edit_page = true;
        } else {
            $_is_super_admin = $_reception_manager->isSuperAdmin();
            $_centre_id = $_reception_manager->isChefDeCentre();
            if (!$_is_super_admin && !$_centre_id) { // Not SUPER_ADMIN and Not ADMIN
                $_show_edit_page = true;
            } elseif ($_centre_id) {
                if ($_centre_id !== $_reception->getCtCentre()->getId()) { // ADMIN cannot edit Reception from another Centre
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
        $_imprimes_use = $_em_imprimes->getCtImprimeTechUseByCtControle($_reception->getId());
        /* ============================================================================================ */

        $_edit_form = $this->createEditForm($_reception);

        return $this->render('AdminBundle:CtReception:edit.html.twig', array(
            'rec_type' => $_reception->getCtTypeReception()->getTprcpLibelle(),
            'reception' => $_reception,
            'imprimes_use' => $_imprimes_use,
            'imprimes_tech' => $_imprimestech,
            'imprimes_tech_use' => $_imprimestechuse,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Affichage page modification reception
     * @param string $_num_group
     * @return Render page
     */
    public function editGroupAction($_num_group)
    {
        // Récupérer manager
//        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
//        $receptions = $_reception_manager->getCtReceptionByNumGroup();
//
//        $_edit_form = $this->createEditForm($_reception);
//
//        return $this->render('AdminBundle:CtReception:edit.html.twig', array(
//            'rec_type' => $_reception->getCtTypeReception()->getTprcpLibelle(),
//            'reception' => $_reception,
//            'edit_form'  => $_edit_form->createView()
//        ));
    }


    /**
     * Affichage page duplicata reception
     * @param CtReception $_reception
     * @return Render page
     */
    public function duplicataAction()
    {
        $_reception  = new CtReception();
        if (!$_reception) {
            throw $this->createNotFoundException('Unable to find type reception entity.');
        }


        $_duplicata_form = $this->createDuplicataForm($_reception);

        return $this->render('AdminBundle:CtReception:duplicata.html.twig', array(
            'reception' => $_reception,
            'duplicata_form'  => $_duplicata_form->createView()
        ));
    }

    public function genererPvAjaxAction(Request $_request)
    {
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        $_response = new JsonResponse();
        $_res = false;

        $_data = $_request->request->all();
        $_num_group = $_data['num_group'];
        $_is_pv_generated = false;
        if ($_num_group) {
            $_return_file = $_reception_manager->generatePVReception($_num_group);
            if (isset($_return_file) && file_exists($_return_file)) {
                $_is_pv_generated = true;
            }
        }
        if ($_is_pv_generated) {
            $_mes = array("num_group" => $_num_group, "success" => true);
        } else {
            $_mes = array("message" => "Erreur de fichier", "success" => false);
        }
        $_response->setData($_mes);
        return $_response;
    }

    /**
     * Generer PV reception
     * @param $id num group
     * @param $reception_id
     * @return Render page
     */
    public function genererPvAction($id)
    {
        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        $_reception = $_reception_manager->getCtReceptionById($id);
        $_num_group = $_reception->getRcpNumGroup();
        $_is_pv_generated = false;
        $_ret = $_reception_manager->generatePVReception($_num_group);
        return $this->redirect($_ret['url_path']);
    }

    /**
     * Generer PV duplicata reception
     * @param $id reception_id
     * @return Render page
     */
    public function genererPvDuplicataAction($id)
    {
        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        //$_is_pv_generated = false;
        $_return_file = $_reception_manager->generatePVDuplicata($id);
        return $this->redirect($_return_file['url_path']);

    }

    /**
     * Generer PV duplicata reception ajax
     * @param Request $_request
     * @return Response
     */
    public function genererPvDuplicataAjaxAction(Request $_request)
    {
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        $_response = new JsonResponse();
        $_res = false;

        $_data = $_request->request->all();
        $_reception_id = $_data['id'];
        $_is_pv_generated = false;
        if ($_reception_id) {
            $_return_file = $_reception_manager->generatePVDuplicata($_reception_id);
            if (isset($_return_file) && file_exists($_return_file)) {
                $_is_pv_generated = true;
            }
        }
        if ($_is_pv_generated) {
            $_mes = array("success" => true);
        } else {
            $_mes = array("message" => "Erreur de fichier", "success" => false);
        }
        $_response->setData($_mes);
        return $_response;
    }

    /**
     * Generer feuille de caisse reception
     * @param $date
     * @param $type
     * @return Render page
     */
    public function genererFeuilleDeCaisseReceptionAction($date, $type)
    {
        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);

        $_date_format = \DateTime::createFromFormat('d_m_Y', $date);
        $_ret = $_reception_manager->generateFeuilleDeCaisse($_date_format, $type);

        return $this->redirect($_ret['url_path']);
    }

    /**
     * Generer fiche de controle reception
     * @param $date
     * @param $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function genererFicheDeControleReceptionAction($date, $type)
    {
        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        $_dt = explode('_', $date);
        $_nw_dt = $_dt[2] . '-' . $_dt[1] . '-' . $_dt[0];
        $_date = new \DateTime($_nw_dt);
        $_ret = $_reception_manager->generateFicheDeControle($_date, $type);
        return $this->redirect($_ret['url_path']);
    }


    public function downloadPvAction($id) {
        $_ext = ".pdf";
        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        $_reception = $_reception_manager->getCtReceptionById($id);
        $_num_group = $_reception->getRcpNumGroup();
        $_download_link = $_reception_manager->generatePVReceptionDownloadLink($_num_group, $_ext);
        $_path = $_download_link['path'];
        $_filename = $_download_link['filename'];
        $content = file_get_contents($_path.$_filename);

        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$_filename);
        if (file_exists($_path.$_filename)) {
            unlink($_path.$_filename);
        }

        $response->setContent($content);
        return $response;
    }

    public function downloadPvDuplicataAction($id) {
        $_ext = ".pdf";
        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        $_reception = $_reception_manager->getCtReceptionById($id);
        $_num_pv = $_reception->getRcpNumPv();

        $_download_link = $_reception_manager->generatePVDuplicataDownloadLink($_num_pv, $_ext);
        $_path = $_download_link['path'];
        $_filename = $_download_link['filename'];
        $content = file_get_contents($_path.$_filename);

        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$_filename);
        if (file_exists($_path.$_filename)) {
            unlink($_path.$_filename);
        }

        $response->setContent($content);
        return $response;
    }

    /**
     * Affichage detail reception
     * @param CtReception $_reception
     * @return Render page
     */
    public function viewAction(CtReception $_reception)
    {
        if (!$_reception) {
            throw $this->createNotFoundException('Unable to find type reception entity.');
        }

        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);

        // Récupérer tout les receptions types
        $_reception = $_reception_manager->getCtReceptionById($_reception->getId());

        return $this->render('AdminBundle:CtReception:view.html.twig', array(
            'reception' => $_reception,
        ));
    }

    /**
     * Redirection a la page edit
     * @param integer $id
     * @return Render page
     */
    public function redirectToEditAction($id)
    {
        $this->get('session')->getFlashBag()->set('success_reception_id', (int)$id); // For security purpose
        return $this->redirectToRoute('reception_edit', array('id' => (int)$id));
    }

    /**
     * Création reception
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request, $type)
    {
        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        $_reception_type_manager = $this->get(ServiceName::SRV_METIER_TYPE_RECEPTION);
        $_req_data = $_request->request->all();
        $_uniqid = $this->get('session')->get('uniqid');
        
        // Récupérer tout les imprimés techniques
        $_imprime_tech = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);
        $_imprime_tech_use = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimestech = $_imprime_tech->getAllCtImprimeTechByOrder(array('nomImprimeTech' => 'ASC'));
        $_imprimestechuse = $_imprime_tech_use->getAllCtImprimeTechNoUsedOrder();

        // Afficher choix type
        if ($type === 0) { // Show choice type reception
            if (isset($_uniqid)) $this->get('session')->remove('uniqid');
            $_reception_type = $_reception_type_manager->getAllCtTypeReception();
            return $this->render('AdminBundle:CtReception:add.html.twig', array(
                'show_first_page' => true,
                'type_receptions' => $_reception_type
            ));
        } else {
            if (!isset($_uniqid)) {
                $this->get('session')->set('uniqid', uniqid());
                $_uniqid = $this->get('session')->get('uniqid');
            }
            $_reception  = new CtReception();
            $_form       = $this->createCreateForm($_reception, $type);
            $_user_connected = $this->get('security.token_storage')->getToken()->getUser();
            if ($type == "isole") {
                $_type_rec = $_reception_type_manager->getTypeReception($type);
                $_form->handleRequest($_request);
                if ($_form->isSubmitted() && $_form->isValid()) {
                    $_genre_vehicule = $_reception->getCtVehicule()->getCtGenre();
                    $_reception->setCtGenre($_genre_vehicule);
                    if (isset($_user_connected) && $_user_connected instanceof User) { // Test if user is connected
                        $_reception->setCtCentre($_user_connected->getCtCentre());
                        $_reception->setCtUser($_user_connected);
                        $_reception->setCtTypeReception($_type_rec);
                    }
                    $_reception = $_reception_manager->setNumGroupById($_reception, $_uniqid);

                    $_ret_recep = $_reception_manager->saveCtReception($_reception, 'new');
                    // Afficher telechargement page
                    if ($_ret_recep) {
                        $_reception_manager->setFlash('success', "Réception ajoutée");

                        /* ============ Misa à jour des imprimés utilisés pour cette visite ============ */
                        $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
                        $_data = $_request->request->all();
                        $_list_itu = $_data['ct_imprime_tech_use'];
                        foreach($_list_itu as $_one_uti){
                            $_imprime_tech_use = $_em_imprimes->getCtImprimeTechUseById($_one_uti);
                            $_em_imprimes->saveCtImprimeTechUse($_imprime_tech_use, 'Réception', $_reception->getId());
                        }
                        /* ============================================================================== */

                        $this->get('session')->getFlashBag()->set('success_reception_id', $_reception->getId()); // For security purpose
                        return $this->redirectToRoute('reception_edit', array('id' => $_reception->getId()));
//                        unset($_form);
//                        unset($_reception);
//                        $_reception = new CtReception();
//                        $_form  = $this->createCreateForm($_reception, $type);

                    }

                }

                /* ====================== Récupération data imprimés techniques utilisés ====================== */
                $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
                $_imprimes_use = $_em_imprimes->getCtImprimeTechUseByCtControle($_reception->getId());
                /* ============================================================================================ */

                // Afficher formulaire
                return $this->render('AdminBundle:CtReception:add.html.twig', array(
                    'rec_type'  => $type,
                    'is_focus'  => 0,
                    'reception' => $_reception,
                    'form'      => $_form->createView(),
                    'imprimes_tech' => $_imprimestech,
                    'imprimes_tech_use' => $_imprimestechuse,
                    'imprimes_use' => $_imprimes_use,
                ));
            } elseif ($type == 'type') {
                $_type_rec = $_reception_type_manager->getTypeReception($type);
                $_nb_total_vehicule = null;
                $_is_stop = false;

                // Immatriculation element n'est pas utilise
                $_form->remove('rcpImmatriculation');

                // Recuperer nombre de vehicule a saisir
                if (array_key_exists('ct_nb_total_vehicule', $_req_data) &&
                    count($_req_data['ct_nb_total_vehicule'])) {
                    $_nb_total_vehicule = $_req_data['ct_nb_total_vehicule'];
                } else if (array_key_exists('nb-total-vehicule', $_req_data) &&
                    count($_req_data['nb-total-vehicule'])) {
                    $_nb_total_vehicule = $_req_data['nb-total-vehicule'];
                } else {
                    return $this->redirectToRoute('reception_new');
                }

                // Numero saisie reception type
                if (array_key_exists('num_saisie', $_req_data)) {
                    $_num_saisie = $_req_data['num_saisie'];
                }

                // Test pour continuer saisie reception type
                if (isset($_nb_total_vehicule) && isset($_num_saisie) &&
                    count($_nb_total_vehicule) > 0 && count($_num_saisie) > 0) {
                    if ($_num_saisie < $_nb_total_vehicule) {// STOP saisie
                        $_is_stop = false;
                    } else {
                        $_is_stop = true;
                    }
                }

                // Set motif manuellement
                $_motif_manager = $this->get(ServiceName::SRV_METIER_MOTIF);
                $_motif = $_motif_manager->getCtMotifByLibelle("VNI");
                $_form->get('ctMotif')->setData($_motif);

                $_form->handleRequest($_request);

                if ($_form->isSubmitted() && $_form->isValid()) {
                    $_genre_vehicule = $_reception->getCtVehicule()->getCtGenre();
                    $_reception->setCtGenre($_genre_vehicule);

                    if (isset($_user_connected) && $_user_connected instanceof User) { // Test if user is connected
                        $_reception->setCtCentre($_user_connected->getCtCentre());
                        $_reception->setCtUser($_user_connected);
                        $_reception->setCtTypeReception($_type_rec);
                    }
                    $_reception = $_reception_manager->setNumGroupById($_reception, $_uniqid);
                    $_reception->setCtMotif($_motif);

                    $_ret_recep = $_reception_manager->saveCtReception($_reception, 'new');

                    if ($_ret_recep) {
                        $_reception_manager->setFlash('success', "Réception ajoutée");
                        unset($_form);
                        $_reception = $_reception_manager->resetVehiculeReception($_reception);
                        $_form  = $this->createCreateForm($_reception, $type);
                        $_motif = $_motif_manager->getCtMotifByLibelle("VNI");
                        $_form->get('ctMotif')->setData($_motif);
                        $_form->remove('rcpImmatriculation');
                        if (isset($_num_saisie) && $_num_saisie > 0)
                            $_num_saisie++;

                        if ($_is_stop) {
                            /* ============ Misa à jour des imprimés utilisés pour cette visite ============ */
                            $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
                            $_data = $_request->request->all();
                            $_list_itu = $_data['ct_imprime_tech_use'];
                            foreach($_list_itu as $_one_uti){
                                $_imprime_tech_use = $_em_imprimes->getCtImprimeTechUseById($_one_uti);
                                $_em_imprimes->saveCtImprimeTechUse($_imprime_tech_use, 'Réception', $_reception->getId());
                            }
                            /* ============================================================================== */

                            $this->get('session')->getFlashBag()->set('success_reception_id', $_reception->getId()); // For security purpose
                            return $this->redirectToRoute('reception_edit', array('id' => $_reception->getId()));
                        }

                        /* ====================== Récupération data imprimés techniques utilisés ====================== */
                        $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
                        $_imprimes_use = $_em_imprimes->getCtImprimeTechUseByCtControle($_reception->getId());
                        /* ============================================================================================ */

                        return $this->render('AdminBundle:CtReception:add.html.twig', array(
                            'id_recep' => $_ret_recep->getId(),
                            'is_stop' => $_is_stop,
                            'num_saisie' => $_num_saisie,
                            'rec_type' => $type,
                            'nb_vehicule' => $_nb_total_vehicule,
                            'is_focus' => 1,
                            'reception' => $_reception,
                            'form'       => $_form->createView(),
                            'imprimes_tech' => $_imprimestech,
                            'imprimes_tech_use' => $_imprimestechuse,
                            'imprimes_use' => $_imprimes_use,
                        ));
                    }

                }

                /* ====================== Récupération data imprimés techniques utilisés ====================== */
                $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
                $_imprimes_use = $_em_imprimes->getCtImprimeTechUseByCtControle($_reception->getId());
                /* ============================================================================================ */

                return $this->render('AdminBundle:CtReception:add.html.twig', array(
                    'is_stop' => $_is_stop,
                    'num_saisie' => 1,
                    'rec_type' => $type,
                    'nb_vehicule' => $_nb_total_vehicule,
                    'is_focus' => 0,
                    'reception' => $_reception,
                    'form'       => $_form->createView(),
                    'imprimes_tech' => $_imprimestech,
                    'imprimes_tech_use' => $_imprimestechuse,
                    'imprimes_use' => $_imprimes_use,
                ));
                return $this->redirectToRoute('reception_new');
            }

            return $this->redirectToRoute('reception_new');
        }
    }

    /**
     * Modification reception
     * @param Request $_request requête
     * @param CtReception $_reception
     * @return Render page
     */
    public function updateAction(Request $_request, CtReception $_reception)
    {
        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        
        // Récupérer tout les imprimés techniques
        $_imprime_tech = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);
        $_imprime_tech_use = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimestech = $_imprime_tech->getAllCtImprimeTechByOrder(array('nomImprimeTech' => 'ASC'));
        $_imprimestechuse = $_imprime_tech_use->getAllCtImprimeTechNoUsedOrder();

        if (!$_reception) {
            throw $this->createNotFoundException('Unable to find reception entity.');
        }

        $_edit_form = $this->createEditForm($_reception);
        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_reception_manager->saveCtReception($_reception, 'update');

            $_reception_manager->setFlash('success', "Type réception modifiée");

            /* ============ Misa à jour des imprimés utilisés pour cette visite ============ */
            $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
            $_data = $_request->request->all();
            $_list_itu = $_data['ct_imprime_tech_use'];
            foreach($_list_itu as $_one_uti){
                $_imprime_tech_use = $_em_imprimes->getCtImprimeTechUseById($_one_uti);
                $_em_imprimes->saveCtImprimeTechUse($_imprime_tech_use, 'Réception', $_reception->getId());
            }
            /* ============================================================================== */

            $this->get('session')->getFlashBag()->set('success_reception_id', $_reception->getId()); // For security purpose

            return $this->redirectToRoute('reception_edit', array('id' => $_reception->getId()));
//            return $this->redirect($this->generateUrl('reception_index'));
        }

        /* ====================== Récupération data imprimés techniques utilisés ====================== */
        $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimes_use = $_em_imprimes->getCtImprimeTechUseByCtControle($_reception->getId());
        /* ============================================================================================ */

        return $this->render('AdminBundle:CtReception:edit.html.twig', array(
            'rec_type' => $_reception->getCtTypeReception()->getTprcpLibelle(),
            'reception' => $_reception,
            'imprimes_tech' => $_imprimestech,
            'imprimes_tech_use' => $_imprimestechuse,
            'imprimes_use' => $_imprimes_use,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Traitement ajax select centre
     * @param Request $_request requête
     * @return Render JsonResponse
     */
    public function selectCentreAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_centre_manager = $this->get(ServiceName::SRV_METIER_CENTRE);
        $_response = new JsonResponse();

        $_centres_array = array();
        $_data = $_request->request->all();
        $_province_id = $_data['province_id'];

        if($_province_id){
            $_centres = $_centre_manager->getRepository()->findBy(array('ctProvince' => $_province_id));
            foreach ($_centres as $_centre) {
                $_ctr        =  new \stdClass();
                $_ctr->id = $_centre->getId();
                $_ctr->libelle = $_centre->getCtrNom();
                $_centres_array[]     = $_ctr;
            }
            $_response->setData($_centres_array);
        } else {
            $_err = array("message" => "Erreur province");
            $_response->setData($_err);
        }

        return $_response;
    }

    /**
     * Traitement ajax autocomplete num_serie
     * @param Request $_request requête
     * @return Render JsonResponse
     */
    public function autoCompleteNumSerieAction(Request $request)
    {
        $_num_series = array();
        $_term = trim(strip_tags($request->get('term')));

        // Récupérer manager
//        $_vehicule_manager = $this->get(ServiceName::SRV_METIER_VEHICULE);
//        $_entities = $_vehicule_manager->getRepository()->createQueryBuilder('v')
//            ->where('v.vhcNumSerie LIKE :name')
//            ->setParameter('name', $_term.'%')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult();

//        foreach ($_entities as $_entity)
//        {
//            $_num_series[] = array(
//                'id' => $_entity->getId(),
//                'num_serie' => $_entity->getVhcNumSerie(),
//                'num_moteur' => $_entity->getVhcNumMoteur(),
//                'genre' => $_entity->getCtGenre()->getId(),
//                'marque' => $_entity->getCtMarque()->getId(),
//                'cylindre' => $_entity->getVhcCylindre(),
//                'puissance' => $_entity->getVhcPuissance(),
//                'charge_utile' => $_entity->getVhcChargeUtile(),
//                'poids_vide' => $_entity->getvhcPoidsVide(),
//            );
//        }
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        $_receptions = $_reception_manager->getCtReceptionByNumSerieVehicule($_term);
        foreach ($_receptions as $_reception){
            $_num_series[] = array(
                'id_reception' => $_reception['rcp_id'],
                'num_serie' => $_reception['rcp_veh_num_serie'],
            );
        }

        $response = new JsonResponse();
        $response->setData($_num_series);

        return $response;
    }

    /**
     * Traitement ajax autocomplete num_serie pour duplicata
     * @param Request $_request requête
     * @return Render JsonResponse
     */
    public function autoCompleteNumSerieDuplicataAction(Request $request)
    {
        $_num_serie = trim(strip_tags($request->get('term')));

        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        $_receptions = $_reception_manager->getCtReceptionByNumSerieVehicule($_num_serie);

        $response = new JsonResponse();
        $response->setData($_receptions);

        return $response;
    }

    /**
     * Traitement ajax autocomplete immatriculation pour duplicata
     * @param Request $_request requête
     * @return Render JsonResponse
     */
    public function autoCompleteImmatriculeDuplicataAction(Request $request)
    {
        $_immatricule = trim(strip_tags($request->get('term')));

        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);
        $_receptions = $_reception_manager->getCtReceptionByImmatriculeVehicule($_immatricule);

        $response = new JsonResponse();
        $response->setData($_receptions);

        return $response;
    }

    /**
     * Création formulaire de creation de reception
     * @param CtReception $_reception The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtReception $_reception, $type)
    {
        $_form = $this->createForm(CtReceptionType::class, $_reception, array(
            'action' => $this->generateUrl('reception_new', array('type' => $type)),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de edition reception
     * @param CtReception $_reception The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtReception $_reception)
    {
        $_form = $this->createForm(CtReceptionType::class, $_reception, array(
            'action' => $this->generateUrl('reception_update', array('id' => $_reception->getId())),
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Création formulaire de duplicata de reception
     * @param CtReception $_reception The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDuplicataForm(CtReception $_reception)
    {
        $_form = $this->createForm(CtReceptionType::class, $_reception, array(
            'action' => $this->generateUrl('reception_duplicata'),
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Suppression reception
     * @param Request $_request requête
     * @param CtReception $_reception
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtReception $_reception)
    {
        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);

        $_form = $this->createDeleteForm($_reception);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_reception_manager->deleteCtReception($_reception);

            $_reception_manager->setFlash('success', 'reception supprimée');
        }

        return $this->redirectToRoute('reception_index');
    }

    /**
     * Création formulaire de suppression reception
     * @param CtReception $_reception The reception entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtReception $_reception)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('reception_delete', array('id' => $_reception->getId())))
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
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_reception_manager->deleteGroupCtReception($_ids);
        }

        $_reception_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('reception_index'));
    }

    /**
     * Recherche reception
     * @param Request $_request requête
     * @return Render page
     */
    public function searchAction(Request $_request)
    {
        if ($_request->isMethod('POST')) {
            // Récupération données formulaires
            $_data_forms   = $_request->request->all();
            $_id_reception = $_data_forms['id'];

            $_url_reception_edit = $this->generateUrl('reception_edit', array('id' => $_id_reception), UrlGeneratorInterface::ABSOLUTE_URL);

            return new Response($_url_reception_edit);
        }

        return $this->render('AdminBundle:CtReception:search.html.twig');
    }

    /**
     * Récupérer l'information réception lors de recherche
     * @param Request $_request requête
     * @return Render page
     */
    public function getInfoAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);

        // Récupération données formulaires
        $_data_forms = $_request->request->all();
        $_numero     = $_data_forms['numero'];
        $_type       = $_data_forms['type'];

        $_reception = $_reception_manager->getInfoByNumero($_numero, $_type);

        $_statut           = array();
        $_statut['statut'] = false;
        if ($_reception) {
            $_statut['statut']       = true;
            $_statut['id_reception'] = $_reception[0]->getId();
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
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);

        // Récupérer les données formulaire
        $_data = $_request->request->all();
        $_term = $_data['term'];

        // Récupérer les listes numéro d'immatriculation
        $_list_numero_immatriculation = $_reception_manager->getListNumeroImmatriculation($_term);

        return $_list_numero_immatriculation;
    }

    /**
     * Autocomplète pour le numéro de série vehicule
     * @param Request $_request
     * @return JsonResponse
     */
    public function showNumeroSerieAjaxAction(Request $_request) {
        // Récupérer manager
        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);

        // Récupérer les données formulaire
        $_data = $_request->request->all();
        $_term = $_data['term'];

        // Récupérer les listes numéro de série
        $_list_numero_serie = $_reception_manager->getListNumeroSerie($_term);

        return $_list_numero_serie;
    }
}
