<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtVisiteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtVisite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CtContreVisiteController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtContreVisiteController extends Controller
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
            $_visites = $_visite_manager->getAllCtContreVisite(array('id' => 'DESC'), $_centre_id);
            return $this->render('AdminBundle:CtContreVisite:index.html.twig', array(
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
        // Récupérer manager
        $_contre_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);
        
        // Récupérer tout les imprimés techniques
        $_imprime_tech = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);
        $_imprime_tech_use = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimestech = $_imprime_tech->getAllCtImprimeTechByOrder(array('nomImprimeTech' => 'ASC'));
        $_imprimestechuse = $_imprime_tech_use->getAllCtImprimeTechNoUsedOrder();

        if (!$_visite) {
            throw $this->createNotFoundException('Unable to find CtVisite entity.');
        }

        $_reception_manager = $this->get(ServiceName::SRV_METIER_RECEPTION);

        $_success_contre_visite_id = $this->get('session')->getFlashBag()->get('success_contre_visite_id');

        if (isset($_success_contre_visite_id) && count($_success_contre_visite_id) > 0 &&
            $_success_contre_visite_id[0] === $_visite->getId()) { // Show edit after new insertion
            $_show_edit_page = true;
        } else {
            $_is_super_admin = $_reception_manager->isSuperAdmin();
            $_centre_id = $_reception_manager->isChefDeCentre();
            if (!$_is_super_admin && !$_centre_id) { // Not SUPER_ADMIN and Not ADMIN
                $_show_edit_page = false;
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

        // Récupérer liste numéro serie et immatriculation
        $_list_numero_serie           = $_contre_visite_manager->getAllNumeroSerieContreVisite();
        $_list_numero_immatriculation = $_contre_visite_manager->getAllNumeroImmatriculationContreVisite();

        $_edit_form = $this->createCreateForm($_visite);

        return $this->render('AdminBundle:CtContreVisite:edit.html.twig', array(
            'visite'                        => $_visite,
            'edit_form'                     => $_edit_form->createView(),
            'list_numero_serie'             => $_list_numero_serie,
            'list_numero_immatriculation'   => $_list_numero_immatriculation,
            'imprimes_use'                  => $_imprimes_use,
            'imprimes_tech'                 => $_imprimestech,
            'imprimes_tech_use'             => $_imprimestechuse,
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
        $_contre_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);

        $_visite = new CtVisite();
        $_form   = $this->createCreateForm($_visite);
        $_form->handleRequest($_request);

        // Récupérer liste numéro serie et immatriculation
        $_list_numero_serie           = $_contre_visite_manager->getAllNumeroSerieContreVisite();
        $_list_numero_immatriculation = $_contre_visite_manager->getAllNumeroImmatriculationContreVisite();
        
        // Récupérer tout les imprimés techniques
        $_imprime_tech = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH);
        $_imprime_tech_use = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
        $_imprimestech = $_imprime_tech->getAllCtImprimeTechByOrder(array('nomImprimeTech' => 'ASC'));
        $_imprimestechuse = $_imprime_tech_use->getAllCtImprimeTechNoUsedOrder();

        if ($_form->isSubmitted() && $_form->isValid()) {
            // Enregistrement visite
            $_id_carte_visite = $_contre_visite_manager->addCtContreVisite($_visite, 'new');

            /* ============ Misa à jour des imprimés utilisés pour cette visite ============ */
            $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
            $_data = $_request->request->all();
            $_list_itu = $_data['ct_imprime_tech_use'];
            foreach($_list_itu as $_one_uti){
                $_imprime_tech_use = $_em_imprimes->getCtImprimeTechUseById($_one_uti);
                $_em_imprimes->saveCtImprimeTechUse($_imprime_tech_use, 'Contre', $_visite->getId());
            }
            /* ============================================================================== */

            $_contre_visite_manager->setFlash('success', "Contre visite ajouté");
            $this->get('session')->getFlashBag()->set('success_contre_visite_id', $_visite->getId()); // For security purpose

            return $this->redirect($this->generateUrl('contre_visite_edit',array('id'=>$_id_carte_visite)));
        }

        return $this->render('AdminBundle:CtContreVisite:add.html.twig', array(
            'visite'                        => $_visite,
            'form'                          => $_form->createView(),
            'list_numero_serie'             => $_list_numero_serie,
            'list_numero_immatriculation'   => $_list_numero_immatriculation,
            'imprimes_tech'                 => $_imprimestech,
            'imprimes_tech_use'             => $_imprimestechuse,
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
        $_contre_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);

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
            $_contre_visite_manager->updateCtContreVisite($_visite);


            /* ============ Misa à jour des imprimés utilisés pour cette visite ============ */
            $_em_imprimes = $this->get(ServiceName::SRV_METIER_IMPRIME_TECH_USE);
            $_data = $_request->request->all();
            $_list_itu = $_data['ct_imprime_tech_use'];
            foreach($_list_itu as $_one_uti){
                $_imprime_tech_use = $_em_imprimes->getCtImprimeTechUseById($_one_uti);
                $_em_imprimes->saveCtImprimeTechUse($_imprime_tech_use, 'Contre', $_visite->getId());
            }
            /* ============================================================================== */

            $_contre_visite_manager->setFlash('success', "Contre visite modifié");
            $this->get('session')->getFlashBag()->set('success_contre_visite_id', $_visite->getId()); // For security purpose

            return $this->redirect($this->generateUrl('contre_visite_new'));
        }

        return $this->render('AdminBundle:CtContreVisite:edit.html.twig', array(
            'visite'    => $_visite,
            'imprimes_tech' => $_imprimestech,
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
            'action' => $this->generateUrl('contre_visite_new'),
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
            'action' => $this->generateUrl('contre_visite_update', array('id' => $_visite->getId())),
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
        $_contre_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);

        $_form = $this->createDeleteForm($_visite);
        $_form->handleRequest($_request);

        if ($_request->isMethod('GET') || ($_form->isSubmitted() && $_form->isValid())) {
            // Suppression visite
            $_contre_visite_manager->deleteCtVisite($_visite);

            $_contre_visite_manager->setFlash('success', 'Contre visite supprimé');
        }

        return $this->redirectToRoute('contre_visite_index');
    }

    /**
     * Création formulaire de suppression visite
     * @param CtVisite $_visite The CtVisite entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CtVisite $_visite)
    {
        return $this->createFormBuilder()
                    ->setAction($this->generateUrl('contre_visite_delete', array('id' => $_visite->getId())))
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
        $_contre_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);

        if ($_request->request->get('_group_delete') !== null) {
            $_ids = $_request->request->get('delete');
            $_contre_visite_manager->deleteGroupCtVisite($_ids);
        }

        $_contre_visite_manager->setFlash('success', 'Eléments sélectionnés supprimés');

        return $this->redirect($this->generateUrl('contre_visite_index'));
    }

    /**
     * Ajax génération reporting carte visite
     * @param Request $_request
     * @return Render page
     */
    public function generateReportingAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_contre_visite_manager = $this->get(ServiceName::SRV_METIER_VISITE);

        // Récupération données formulaires
        $_data_forms = $_request->request->all();
        $_id_visite  = $_data_forms['id'];

        $_visite = $_contre_visite_manager->getCtVisiteById($_id_visite);

        $_link_download = $_contre_visite_manager->generateReporting($_visite);

        return new Response($_link_download);
    }

    /**
     * Recherche visite par numéro de série
     * @param Request $_request requête
     * @return Render page
     */
    public function searchBySerieAction(Request $_request)
    {
        // Récupérer manager
        $_carte_grise_manager    = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);
        $_carte_vehicule_manager = $this->get(ServiceName::SRV_METIER_VEHICULE);

        // Récupération données formulaires
        $_data_forms   = $_request->request->all();
        $_numero_serie = $_data_forms['ct_numero_serie'];

        // Récupération visite
        $_vehicule    = $_carte_vehicule_manager->findByNumeroSerie($_numero_serie);
        $_carte_grise = $_carte_grise_manager->findByVehicule($_vehicule[0]);
        $_visite      = $_carte_grise_manager->getLastVisiteByCarteGrise($_carte_grise[0]);
        $this->get('session')->getFlashBag()->set('success_contre_visite_id', $_visite[0]->getId()); // For security purpose

        return $this->redirect($this->generateUrl('contre_visite_edit', array('id' => $_visite[0]->getId())));
    }

    /**
     * Recherche visite par numéro d'immatriculation
     * @param Request $_request requête
     * @return Render page
     */
    public function searchByImmatriculationAction(Request $_request)
    {
        // Récupérer manager
        $_carte_grise_manager    = $this->get(ServiceName::SRV_METIER_CARTE_GRISE);

        // Récupération données formulaires
        $_data_forms             = $_request->request->all();
        $_numero_immatriculation = $_data_forms['ct_numero_immatriculation'];

        // Récupération visite
        $_carte_grise = $_carte_grise_manager->findByImmatriculation($_numero_immatriculation);
        $_visite      = $_carte_grise_manager->getLastVisiteByCarteGrise($_carte_grise[0]);
        $this->get('session')->getFlashBag()->set('success_contre_visite_id', $_visite[0]->getId()); // For security purpose

        return $this->redirect($this->generateUrl('contre_visite_edit', array('id' => $_visite[0]->getId())));
    }
}
