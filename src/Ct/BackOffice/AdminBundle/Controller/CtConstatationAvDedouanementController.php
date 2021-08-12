<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Entity\CtConstAvDed;
use Ct\Service\MetierManagerBundle\Entity\CtConstAvDedCarac;
use Ct\Service\MetierManagerBundle\Form\CtConstatationAvDedouanementCaracType;
use Ct\Service\MetierManagerBundle\Form\CtConstatationAvDedouanementType;
use Ct\Service\MetierManagerBundle\Utils\PathReportingName;
use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class CtConstatationAvDedouanementController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtConstatationAvDedouanementController extends Controller
{
    /**
     * Afficher tout les constatation avant dedouanements
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_const_av_ded_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED);

        // Récupérer tout les carosséries
        $_constatations = $_const_av_ded_manager->getAllCtConstatationAvDedouanementByOrder(array('id' => 'DESC'));

        return $this->render('AdminBundle:CtConstatationAvDedouanement:index.html.twig', array(
            'constatations' => $_constatations,
        ));
    }

    /**
     * Affichage page modification constatation avant dedouanement
     * @param CtConstAvDed $_const_av_ded
     * @return Render page
     */
    public function editAction(CtConstAvDed $_const_av_ded)
    {
        if (!$_const_av_ded) {
            throw $this->createNotFoundException('Unable to find constatation avant dedouanement entity.');
        }

        // Récupérer tout les imprimés techniques
        $_imprime_tech = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);
        $_imprime_tech_use = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimestech = $_imprime_tech->getAllCtImprimeTechByOrder(array('nomImprimeTech' => 'ASC'));
        $_imprimestechuse = $_imprime_tech_use->getAllCtImprimeTechNoUsedOrder();

        $_edit_form = $this->createEditForm($_const_av_ded);

        $_form_elements = $_edit_form->get('ctConstAvDedCaracs');
        foreach ($_form_elements as $_form_element) {
            $_const_av_ded_type_value = $_form_element->get('ctConstAvDedType')->getData()->getCadTpLibelle();
            $_form_element->get('cadInputCaracType')->setData($_const_av_ded_type_value);

        }

        /* ====================== Récupération data imprimés techniques utilisés ====================== */
        $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimes_use = $_em_imprimes->getCtImprimeTechUseByCtControle($_const_av_ded->getId());
        /* ============================================================================================ */

        return $this->render('AdminBundle:CtConstatationAvDedouanement:edit.html.twig', array(
            'constatation avant dedouanement' => $_const_av_ded,
            'imprimes_tech' => $_imprimestech,
            'imprimes_tech_use' => $_imprimestechuse,
            'imprimes_use' => $_imprimes_use,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création constatation avant dedouanement
     * @param Request $_request requête
     * @return Render page
     */
    public function newAction(Request $_request)
    {
        // Récupérer manager
        $_const_av_ded_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED);
        $_cadct_manager = $this->get(ServiceName::SRV_METIER_TYPE_CONST_AV_DED); // Const_av_ded_carac_type_manager

        $_province_manager  = $this->get(ServiceName::SRV_METIER_PROVINCE);
        $_centre_manager    = $this->get(ServiceName::SRV_METIER_CENTRE);
        $_verif_manager     = $this->get(ServiceName::SRV_METIER_USER);

        // Récupérer tout les imprimés techniques
        $_imprime_tech = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);
        $_imprime_tech_use = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimestech = $_imprime_tech->getAllCtImprimeTechByOrder(array('nomImprimeTech' => 'ASC'));
        $_imprimestechuse = $_imprime_tech_use->getAllCtImprimeTechNoUsedOrder();

        $_const_av_ded = new CtConstAvDed();

        $_const_av_ded_carac_1 = new CtConstAvDedCarac();
        $_ct_type_const = $_cadct_manager->getCtTypeConstatationAvDedouanementByLibelle("notice");
        $_const_av_ded_carac_1->setCtConstAvDedType($_ct_type_const); // Notice descriptive
        $_const_av_ded->getCtConstAvDedCaracs()->add($_const_av_ded_carac_1);

        $_const_av_ded_carac_3 = new CtConstAvDedCarac();
        $_ct_type_const = $_cadct_manager->getCtTypeConstatationAvDedouanementByLibelle("grise");
        $_const_av_ded_carac_3->setCtConstAvDedType($_ct_type_const); // Corps du vehicule
        $_const_av_ded->getCtConstAvDedCaracs()->add($_const_av_ded_carac_3);

        $_const_av_ded_carac_2 = new CtConstAvDedCarac();
        $_ct_type_const = $_cadct_manager->getCtTypeConstatationAvDedouanementByLibelle("corps");
        $_const_av_ded_carac_2->setCtConstAvDedType($_ct_type_const); // Corps du vehicule
        $_const_av_ded->getCtConstAvDedCaracs()->add($_const_av_ded_carac_2);

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

        $_form       = $this->createCreateForm($_const_av_ded);
        $_form_elements = $_form->get('ctConstAvDedCaracs');

        foreach ($_form_elements as $_form_element) {
            $_const_av_ded_type_value = $_form_element->get('ctConstAvDedType')->getData()->getCadTpLibelle();
            $_form_element->get('cadInputCaracType')->setData($_const_av_ded_type_value);

        }
        $_form->handleRequest($_request);

        $_user_connected = $this->get('security.token_storage')->getToken()->getUser();
        $_ct_centre = $_user_connected->getCtCentre();

        if ($_form->isSubmitted() && $_form->isValid()) {

            $_const_av_ded->setCtCentre($_ct_centre);
            // Enregistrement constatation avant dedouanement
            $_const_av_ded_manager->saveCtConstatationAvDedouanement($_const_av_ded, 'new');

            /* ============ Misa à jour des imprimés utilisés pour cette visite ============ */
            $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
            $_data = $_request->request->all();
            $_list_itu = $_data['ct_imprime_tech_use'];
            foreach($_list_itu as $_one_uti){
                $_imprime_tech_use = $_em_imprimes->getCtImprimeTechUseById($_one_uti);
                $_em_imprimes->saveCtImprimeTechUse($_imprime_tech_use, 'Constatation', $_const_av_ded->getId());
            }
            /* ============================================================================== */

            /* ====================== Récupération data imprimés techniques utilisés ====================== */
            $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
            $_imprimes_use = $_em_imprimes->getCtImprimeTechUseByCtControle($_const_av_ded->getId());
            /* ============================================================================================ */

            $_const_av_ded_manager->setFlash('success', "Constatation avant dedouanement ajoutée");

//            return $this->redirect($this->generateUrl('const_av_ded_index'));
            return $this->render('AdminBundle:CtConstatationAvDedouanement:add.html.twig', array(
                'is_generated' => true,
                'const_av_ded' => $_const_av_ded,
                'form'       => $_form->createView(),
                'centres'   => $_centres,
                'provinces' => $_provinces,
                'verificateurs' => $_verificateurs,
                'imprimes_use' => $_imprimes_use,
                'imprimes_tech' => $_imprimestech,
                'imprimes_tech_use' => $_imprimestechuse,
            ));
        } else {
            $string = (string) $_form->getErrors(true, false);
        }

        /* ====================== Récupération data imprimés techniques utilisés ====================== */
        $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimes_use = $_em_imprimes->getCtImprimeTechUseByCtControle($_const_av_ded->getId());
        /* ============================================================================================ */
        
        return $this->render('AdminBundle:CtConstatationAvDedouanement:add.html.twig', array(
            'const_av_ded' => $_const_av_ded,
            'form'       => $_form->createView(),
            'centres'   => $_centres,
            'provinces' => $_provinces,
            'verificateurs' => $_verificateurs,
            'imprimes_use' => $_imprimes_use,
            'imprimes_tech' => $_imprimestech,
            'imprimes_tech_use' => $_imprimestechuse,
        ));
    }

    /**
     * Affichage detail reception
     * @param CtConstAvDed $_const_av_ded
     * @return Render page
     */
    public function viewAction(CtConstAvDed $_const_av_ded)
    {
        if (!$_const_av_ded) {
            throw $this->createNotFoundException('Unable to find type reception entity.');
        }

        // Récupérer manager
        $_const_av_ded_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED);

        // Récupérer tout les receptions types
        $_const_av_ded = $_const_av_ded_manager->getCtConstatationAvDedouanementById($_const_av_ded->getId());

        return $this->render('AdminBundle:CtConstatationAvDedouanement:view.html.twig', array(
            'const_av_ded' => $_const_av_ded,
        ));
    }

    /**
     * Generer PV constatation
     * @param $id Constatation Id
     * @param $reception_id
     * @return Render page
     */
    public function genererPvAction($id)
    {
        // Récupérer manager
        $_const_av_ded_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED);
        $_is_pv_generated = false;
        $_return_file = $_const_av_ded_manager->generatePVConstatation($id);
        return $this->redirect($_return_file['url_path']);
    }

    public function downloadPvAction($id) {
        $_const_av_ded_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED);
        $_download_link = $_const_av_ded_manager->generatePVConstatationDownloadLink($id);
        $_path = $_download_link['path'];
        $_filename = $_download_link['filename'];
        $content = file_get_contents($_path.$_filename);

        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$_filename);

        $response->setContent($content);
        return $response;
    }

    /**
     * Modification constatation avant dedouanement
     * @param Request $_request requête
     * @param CtConstAvDed $_const_av_ded
     * @return Render page
     */
    public function updateAction(Request $_request, CtConstAvDed $_const_av_ded)
    {
        // Récupérer manager
        $_const_av_ded_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED);

        // Récupérer tout les imprimés techniques
        $_imprime_tech = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);
        $_imprime_tech_use = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimestech = $_imprime_tech->getAllCtImprimeTechByOrder(array('nomImprimeTech' => 'ASC'));
        $_imprimestechuse = $_imprime_tech_use->getAllCtImprimeTechNoUsedOrder();

        if (!$_const_av_ded) {
            throw $this->createNotFoundException('Unable to find constatation avant dedouanement entity.');
        }

        $_edit_form = $this->createEditForm($_const_av_ded);

        $_edit_form->handleRequest($_request);

        if ($_edit_form->isValid()) {
            $_const_av_ded_manager->saveCtConstatationAvDedouanement($_const_av_ded, 'update');

            /* ============ Misa à jour des imprimés utilisés pour cette visite ============ */
            $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
            $_data = $_request->request->all();
            $_list_itu = $_data['ct_imprime_tech_use'];
            foreach($_list_itu as $_one_uti){
                $_imprime_tech_use = $_em_imprimes->getCtImprimeTechUseById($_one_uti);
                $_em_imprimes->saveCtImprimeTechUse($_imprime_tech_use, 'Constatation', $_const_av_ded->getId());
            }
            /* ============================================================================== */

            $_const_av_ded_manager->setFlash('success', "Constatation avant dedouanement modifiée");

            return $this->redirect($this->generateUrl('const_av_ded_index'));
        }

        return $this->render('AdminBundle:CtConstatationAvDedouanement:edit.html.twig', array(
            'const_av_ded' => $_const_av_ded,
            'edit_form'  => $_edit_form->createView()
        ));
    }

    /**
     * Création formulaire d'édition constatation avant dedouanement
     * @param CtConstAvDed $_const_av_ded The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CtConstAvDed $_const_av_ded)
    {
        $_user_connected = $this->get('security.token_storage')->getToken()->getUser();
        $_form = $this->createForm(CtConstatationAvDedouanementType::class, $_const_av_ded, array(
            'action' => $this->generateUrl('const_av_ded_new'),
            'user_connected' => $_user_connected,
            'method' => 'POST'
        ));

        return $_form;
    }

    /**
     * Création formulaire de création constatation avant dedouanement
     * @param CtConstAvDed $_const_av_ded The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CtConstAvDed $_const_av_ded)
    {
        $_user_connected = $this->get('security.token_storage')->getToken()->getUser();
        $_form = $this->createForm(CtConstatationAvDedouanementType::class, $_const_av_ded, array(
            'action' => $this->generateUrl('const_av_ded_update', array('id' => $_const_av_ded->getId())),
            'user_connected' => $_user_connected,
            'method' => 'PUT',
        ));

        return $_form;
    }

    /**
     * Suppression constatation avant dedouanement
     * @param Request $_request requête
     * @param CtConstAvDed $_const_av_ded
     * @return Redirect redirection
     */
    public function deleteAction(Request $_request, CtConstAvDed $_const_av_ded)
    {
        // Récupérer manager
        $_const_av_ded_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED);

        $_form = $this->createDeleteForm($_const_av_ded);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression carossérie
            $_const_av_ded_manager->deleteCtConstatationAvDedouanement($_const_av_ded);

            $_const_av_ded_manager->setFlash('success', 'Constatation avant dedouanement supprimée');
        }

        return $this->redirectToRoute('const_av_ded_index');
    }

    /**
     * Création formulaire de suppression constatation avant dedouanement
     * @param CtConstAvDed $_const_av_ded The constatation avant dedouanement entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtConstAvDed $_const_av_ded)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('const_av_ded_delete', array('id' => $_const_av_ded->getId())))
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
        $_const_av_ded_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED_CARAC);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_const_av_ded_manager->deleteGroupCtConstatationAvDedouanement($_ids);
        }

        $_const_av_ded_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('const_av_ded_index'));
    }

    /**
     * Ajax génération reporting feuille de caisse
     * @param Request $_request
     * @return JsonResponse
     */
    public function generateReportingFeuilleCaisseAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_constatation_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED);

        // Récupération données formulaires
        $_data_forms = $_request->request->all();
        $_date       = array_key_exists('date', $_data_forms) ? $_data_forms['date'] : date('d/m/Y');
        $_centre_id  = array_key_exists('centre_id', $_data_forms) ? $_data_forms['centre_id'] : 0;

        // Formattage date
        $_date_exploded = explode('/', $_date);
        $_date_parsed   = $_date_exploded[2] . '-' . $_date_exploded[1] . '-' . $_date_exploded[0];

        $_link_download = $_constatation_manager->generateFeuilleCaisse($_date_parsed, $_centre_id);

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
        $_constatation_manager = $this->get(ServiceName::SRV_METIER_CONST_AV_DED);

        // Récupération données formulaires
        $_data_forms      = $_request->request->all();
        $_date            = array_key_exists('date', $_data_forms) ? $_data_forms['date'] : date('d/m/Y');
        $_id_centre       = array_key_exists('centre_id', $_data_forms) ? $_data_forms['centre_id'] : 0;
        $_id_verificateur = array_key_exists('verif_id', $_data_forms) ? $_data_forms['verif_id'] : 0;

        // Formater la date français en anglais
        $_date_parsed = \DateTime::createFromFormat('d/m/Y', $_date)->format('Y-m-d');

        $_link_download = $_constatation_manager->generateFicheDeControle($_date_parsed, $_id_centre, $_id_verificateur);

        return new JsonResponse($_link_download);
    }
}
