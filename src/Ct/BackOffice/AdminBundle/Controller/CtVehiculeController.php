<?php

namespace Ct\BackOffice\AdminBundle\Controller;

use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Ct\Service\MetierManagerBundle\Form\CtAnomalieType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Ct\Service\MetierManagerBundle\Entity\CtAnomalie;

/**
 * Class CtVehiculeController
 *
 * @package Ct\BackOffice\AdminBundle\Controller
 */
class CtVehiculeController extends Controller
{
    /**
     * Autocomplète pour le numéro de série
     * @param Request $_request
     * @return JsonResponse
     */
    public function showNumeroSerieAjaxAction(Request $_request) {
        // Récupérer manager
        $_vehicule_manager = $this->get(ServiceName::SRV_METIER_VEHICULE);

        // Récupérer les données formulaire
        $_data = $_request->request->all();
        $_term = $_data['term'];

        // Récupérer les listes numéro de série
        $_list_numero_serie = $_vehicule_manager->getListNumeroSerie($_term);

        return $_list_numero_serie;
    }
}
