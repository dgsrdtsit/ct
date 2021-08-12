<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CtHistoriqueController
 *
 * @package AdminBundle\Controller
 */
class CtHistoriqueController extends Controller
{
    /**
     * Afficher l'historique
     * @param Request $_request
     * @return Render page
     */
    public function indexAction(Request $_request)
    {
        // Récupérer manager
        $_historique_manager = $this->get(ServiceName::SRV_METIER_HISTORIQUE);

        $_receptions     = '';
        $_carte_grises   = '';
        $_visites        = '';
        $_contre_visites = '';
        $_numero_serie_search = '';
        $_numero_immatriculation_search = '';

        if ($_request->getMethod() === 'POST' ) {
            // Récupération données formulaires
            $_data_forms   = $_request->request->all();
            $_numero_serie_search = isset($_data_forms['ct_numero_serie']) ? $_data_forms['ct_numero_serie'] : '';
            $_numero_immatriculation_search = isset($_data_forms['ct_numero_immatriculation']) ? $_data_forms['ct_numero_immatriculation'] : '';
            $_numero_serie = isset($_data_forms['ct_numero_serie']) ? $_data_forms['ct_numero_serie'] : $_data_forms['ct_numero_immatriculation'];

            // Recherche par numéro de série
            $_receptions     = $_historique_manager->getAllReceptionByNumeroSerie($_numero_serie);
            $_carte_grises   = $_historique_manager->getAllCarteVisiteByNumeroSerie($_numero_serie);
            $_visites        = $_historique_manager->getAllVisiteByNumeroSerie($_numero_serie);
            $_contre_visites = $_historique_manager->getAllContreVisiteByNumeroSerie($_numero_serie);
        }

        return $this->render('AdminBundle:CtHistorique:index.html.twig', array(
            'receptions'     => $_receptions,
            'cartes_grise'   => $_carte_grises,
            'visites'        => $_visites,
            'contre_visites' => $_contre_visites,
            'numero_serie_search' => $_numero_serie_search,
            'numero_immatriculation_search' => $_numero_immatriculation_search
        ));
    }

    public function getLogByCentreAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_hist_manager = $this->get(ServiceName::SRV_METIER_HISTORIQUE);
        $_user_manager = $this->get(ServiceName::SRV_METIER_USER);

        $_response = new JsonResponse();
        $_data = $_request->request->all();
        $_user_id = isset($_data['user_id']) ? $_data['user_id'] : 0;

        if ($_user_manager->isUserConnected($_user_manager->getUserById($_user_id))) {
            if ($_user_manager->isChefDeCentre() > 0) {
                $_centre_id = $_user_manager->isChefDeCentre();
            } elseif ($_user_manager->isSuperAdmin()) {
                $_centre_id = 0;
            }
        }

        $_notifications = $_hist_manager->getNotificationByCentre($_centre_id);
        $_array_of_notif = array();
        foreach ($_notifications as $_notif) {
            $_notif_id = $_notif->getId();
            $_notif_date = $_notif->getHstDateCreate();
            $_notif_type = $_notif->getHstType();
            $_array_of_notif[] = array(
                'notif_id' => $_notif_id,
                'notif_type' => $_notif_type,
                'notif_date' => $_notif_date->format('d/m/Y H:i:s')
            );
        }
        if (count($_array_of_notif) > 0) {
            $_data_res = array(
                'is_notif' => true,
                'notif' => $_array_of_notif,
                'total_notif' => count($_array_of_notif)
            );
        } else {
            $_data_res = array(
                'is_notif' => false,
            );
        }
        $_response->setData($_data_res);
        return $_response;
    }

    public function getDetailLogAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_hist_manager = $this->get(ServiceName::SRV_METIER_HISTORIQUE);

        $_response = new JsonResponse();
        $_data = $_request->request->all();
        $_historique_id = isset($_data['historique_id']) ? $_data['historique_id'] : 0;        

        $_hist_detail = $_hist_manager->getDetailNotification($_historique_id);
        if (isset($_hist_detail)) {
            $_notification = $_hist_detail;
        } else {
            $_notification = array();
        }

        $_response->setData($_notification);
        return $_response;
    }

    public function getDetailAllLogAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_hist_manager = $this->get(ServiceName::SRV_METIER_HISTORIQUE);

        $_response = new JsonResponse();
        $_hist_details = $_hist_manager->getDetailAllNotifications();
        $_response->setData($_hist_details);
        return $_response;
    }
}
