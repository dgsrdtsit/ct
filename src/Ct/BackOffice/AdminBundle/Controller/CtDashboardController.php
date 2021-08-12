<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Utils\RoleName;

/**
 * Class DashboardController
 *
 * @package AdminBundle\Controller
 */
class CtDashboardController extends Controller
{
    /**
     * Afficher le tableau de bord
     * @return Render page
     */
    public function indexAction()
    {
        $_statistique_visite_manager = $this->get(ServiceName::SRV_METIER_STATISTIQUE_VISITE);
        $_user_connected = $this->container->get('security.token_storage')->getToken()->getUser();
        $_is_centre = false;
        $_id_centre = null;
        if ($_user_connected->getCtRole()->getId() != RoleName::ID_ROLE_SUPERADMIN) {
            $_is_centre = true;
            $_user_centre = $_user_connected->getCtCentre();
            $_id_centre   = $_user_centre->getId();
        }
        $_statistique = $_statistique_visite_manager->getAllStatistique($_id_centre, $_is_centre);
        return $this->render('AdminBundle:CtDashboard:index.html.twig', array('statistique' => $_statistique));
    }
}
