<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtVisiteType;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtVisite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CtStatistiqueConstatationController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtStatistiqueConstatationController extends Controller
{
    /**
     * Afficher la page génération statistique constatation
     * @return Render page
     */
    public function indexAction()
    {
        // Récupérer manager
        $_province_manager = $this->get(ServiceName::SRV_METIER_PROVINCE);
        $_centre_manager   = $this->get(ServiceName::SRV_METIER_CENTRE);
        $_rec_type_manager = $this->get(ServiceName::SRV_METIER_TYPE_RECEPTION);

        // Récupérer tout les provinces et les centres
        $_centres   = $_centre_manager->getAllCtCentreByOrder(array('id' => 'ASC'));
        $_provinces = $_province_manager->getAllCtProvinceByOrder(array('id' => 'ASC'));
        $_types_rec = $_rec_type_manager->getAllCtTypeReceptionByOrder(array('id' => 'ASC'));

//        $_statistique_constatation_manager = $this->get(ServiceName::SRV_METIER_STATISTIQUE_CONSTATATION);
//        $_statistique_constatation_manager->generateReportingByInterval(4, 2017, 8, 'mensuel');

        return $this->render('AdminBundle:CtStatistiqueConstatation:index.html.twig', array(
            'centres'   => $_centres,
            'provinces' => $_provinces,
            'types_rec' => $_types_rec
        ));
    }

    /**
     * Ajax génération reporting
     * @param Request $_request
     * @return Render page
     */
    public function generateReportingConstatationAjaxAction(Request $_request)
    {
        // Récupérer manager
        $_statistique_constatation_manager = $this->get(ServiceName::SRV_METIER_STATISTIQUE_CONSTATATION);

        // Récupérer le centre de l'utilisateur connecté
        $_user_connected = $this->container->get('security.token_storage')->getToken()->getUser();

        // Récupération données formulaires
        $_data_forms            = $_request->request->all();
        $_id_centre             = $_data_forms['id_centre'];
        $_annee                 = $_data_forms['annee'];
        $_type_interval         = $_data_forms['type'];
        $_value                 = $_data_forms['value'];

        // Récupérer le centre du chef de centre (si le rôle a de type chef de centre)
        if ($_user_connected->getCtRole()->getId() != RoleName::ID_ROLE_SUPERADMIN) {
            $_user_centre = $_user_connected->getCtCentre();
            $_id_centre   = $_user_centre->getId();
        }

        $_res = $_statistique_constatation_manager->generateReportingByInterval($_id_centre, $_annee, $_value, $_type_interval);
        $_download_path = $_res['download_path'];
        $_url_path      = $_res['url_path'];

        return new Response($_url_path);
    }
}
