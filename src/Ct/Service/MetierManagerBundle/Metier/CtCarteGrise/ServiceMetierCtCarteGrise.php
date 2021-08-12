<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtCarteGrise;

use Ct\Service\MetierManagerBundle\Entity\CtCarteGrise;
use Ct\Service\MetierManagerBundle\Entity\CtVehicule;
use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;

class ServiceMetierCtCarteGrise
{
    private $_entity_manager;
    private $_container;

    public function __construct(EntityManager $_entity_manager, Container $_container)
    {
        $this->_entity_manager = $_entity_manager;
        $this->_container = $_container;
    }

    /**
     * Ajouter un message flash
     * @param string $_type
     * @param string $_message
     * @return mixed
     */
    public function setFlash($_type, $_message)
    {
        return $this->_container->get('session')->getFlashBag()->add($_type, $_message);
    }

    /**
     * Récuperer le repository carte grise
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_CARTE_GRISE);
    }

    /**
     * Récuperer tout les carte grises
     * @return array
     */
    public function getAllCtCarteGrise()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les carte grises par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtCarteGriseByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un carte grise par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtCarteGriseById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Ajouter un carte grise
     * @param CtCarteGrise $_carte_grise
     * @return boolean
     */
    public function addCtCarteGrise($_carte_grise)
    {
        // Récupérer manager
        $_vehicule_manager = $this->_container->get(ServiceName::SRV_METIER_VEHICULE);

        // Tester si le numéro de série voiture existe déjà
        $_numero_serie = $_carte_grise->getCtVehicule()->getVhcNumSerie();
        $_numero_serie_exist = $_vehicule_manager->findByNumeroSerie($_numero_serie);
        if ($_numero_serie_exist) {
            $_carte_grise->setCtVehicule($_numero_serie_exist[0]);
        }

        // Récupérer l'utilisateur connecté
        $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();

        // Traitement centre
        if ($_user_connected->getCtRole()->getId() != RoleName::ID_ROLE_SUPERADMIN) {
            $_user_centre = $_user_connected->getCtCentre();
            $_carte_grise->setCtCentre($_user_centre);
        }

        $this->saveCtCarteGrise($_carte_grise, 'new');
    }

    /**
     * Modifier un carte grise
     * @param Entity $_carte_grise
     * @return boolean
     */
    public function updateCtCarteGrise($_carte_grise)
    {
        $this->saveCtCarteGrise($_carte_grise, 'update');
    }

    /**
     * Enregistrer un carte grise
     * @param CtCarteGrise $_carte_grise
     * @param string $_action
     * @return boolean
     */
    public function saveCtCarteGrise($_carte_grise, $_action)
    {
//        $_date_inconnue = new \DateTime('2011-01-01');
//        if (empty($_carte_grise->getCgDateCarteViolette())) {
//            $_carte_grise->setCgDateCarteViolette($_date_inconnue);
//        }
//        if (empty($_carte_grise->getCgMiseEnService())) {
//            $_carte_grise->setCgMiseEnService($_date_inconnue);
//        }

        if ($_action == 'new') {
            $this->_entity_manager->persist($_carte_grise);
        } elseif ($_action == 'update') {
            $this->_entity_manager->persist($_carte_grise);
            // Historique modification
            $_historique_manager = $this->_container->get(ServiceName::SRV_METIER_HISTORIQUE);
            $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();
            $_date_time_modif = new \DateTime();
            $_date_time_modif = $_date_time_modif->format("d/m/Y H:i:s");
            $_historique_manager->logActionUser($_user_connected, $_carte_grise, $_date_time_modif);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un carte grise
     * @param CtCarteGrise $_carte_grise
     * @return boolean
     */
    public function deleteCtCarteGrise($_carte_grise)
    {
        $this->_entity_manager->remove($_carte_grise);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un carte grise
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtCarteGrise($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_carte_grise = $this->getCtCarteGriseById($_id);
                $this->deleteCtCarteGrise($_carte_grise);
            }
        }

        return true;
    }

    /**
     * Récupérer un carte grise par immatriculation
     * @param string $_immatriculation
     * @return array
     */
    public function findByImmatriculation($_immatriculation)
    {
        return $this->getRepository()->findByCgImmatriculation($_immatriculation);
    }

    /**
     * Récupérer un carte grise par numéro d'identification
     * @param string $_num_identification
     * @return array
     */
    public function findByNumeroIdentification($_num_identification)
    {
        return $this->getRepository()->findByCgNumIdentification($_num_identification);
    }

    /**
     * Récupérer un carte grise par vehicule
     * @param CtVehicule $_vehicule
     * @return array
     */
    public function findByVehicule($_vehicule)
    {
        return $this->getRepository()->findByCtVehicule($_vehicule->getId());
    }

    /**
     * Récupérer liste json numéro d'immatriculation
     * @param string $_term
     * @return array
     */
    public function getListNumeroImmatriculation($_term)
    {
        $_entity = EntityName::CT_CARTE_GRISE;
        $_dql    = "SELECT c.cgImmatriculation FROM $_entity c
                    WHERE c.cgImmatriculation LIKE :term";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('term', $_term . '%');
        $_query->setMaxResults(10);

        $_results = $_query->getResult();

        // Traitement réponse Json
        $_numero_immatriculations = array();
        foreach ($_results as $_result) {
            $_numero_immatriculations[] = $_result['cgImmatriculation'];
        }

        $_response = new JsonResponse();
        $_response->setData($_numero_immatriculations);

        return $_response;
    }

    /**
     * Récupérer liste json numéro d'identification
     * @param string $_term
     * @return array
     */
    public function getListNumeroIdentification($_term)
    {
        $_entity = EntityName::CT_CARTE_GRISE;
        $_dql    = "SELECT c.cgNumIdentification FROM $_entity c
                    WHERE c.cgNumIdentification LIKE :term";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('term', $_term . '%');
        $_query->setMaxResults(10);

        $_results = $_query->getResult();

        // Traitement réponse Json
        $_numero_identifications = array();
        foreach ($_results as $_result) {
            $_numero_identifications[] = $_result['cgNumIdentification'];
        }

        $_response = new JsonResponse();
        $_response->setData($_numero_identifications);

        return $_response;
    }

    /**
     * Récupérer la carte grise par numéro de série
     * @param string $_numero_serie
     * @return array
     */
    public function getCarteGriseByNumeroSerie($_numero_serie)
    {
        $_entity = EntityName::CT_CARTE_GRISE;
        $_dql    = "SELECT c FROM $_entity c
                    LEFT JOIN c.ctVehicule v 
                    WHERE v.vhcNumSerie = :numero_serie";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('numero_serie', $_numero_serie);

        return $_query->getResult();
    }

    /**
     * Récupérer liste json numéro de série vehicule
     * @param string $_term
     * @return array
     */
    public function getListNumeroSerie($_term)
    {
        $_entity = EntityName::CT_CARTE_GRISE;
        $_dql    = "SELECT v.vhcNumSerie FROM $_entity c
                    LEFT JOIN c.ctVehicule v
                    WHERE v.vhcNumSerie LIKE :term";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('term', $_term . '%');
        $_query->setMaxResults(10);

        $_results = $_query->getResult();

        // Traitement réponse Json
        $_numero_series = array();
        foreach ($_results as $_result) {
            $_numero_series[] = $_result['vhcNumSerie'];
        }

        $_response = new JsonResponse();
        $_response->setData($_numero_series);

        return $_response;
    }

    /**
     * Récupérer le dernier visite technique par carte grise
     * @param CtCarteGrise $_carte_grise
     * @return array
     */
    public function getLastVisiteByCarteGrise(CtCarteGrise $_carte_grise)
    {
        $_entity = EntityName::CT_VISITE;
        $_dql    = "SELECT v FROM $_entity v
                    LEFT JOIN v.ctCarteGrise c 
                    WHERE c.id = :id_carte_grise
                    ORDER BY v.id DESC";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('id_carte_grise', $_carte_grise->getId());
        $_query->setMaxResults(1);

        return $_query->getResult();
    }

    /**
     * Récupérer l'information carte grise par type (numéro de série ou immatriculation)
     * @param string $_numero
     * @param string $_type
     * @return array
     */
    public function getArrayInfoCarteGriseByNumero($_numero, $_type)
    {
        if ($_type == 'serie') {
            // Récupérer manager
            $_vehicule_manager = $this->_container->get(ServiceName::SRV_METIER_VEHICULE);
            $_vehicule = $_vehicule_manager->findByNumeroSerie($_numero);

            $_carte_grise = $this->getRepository()->findByCtVehicule($_vehicule);
        } else {
            $_carte_grise = $this->getRepository()->findByCgImmatriculation($_numero);
        }

        // Traitement tableau
        $_array_carte_grise = array();
        if ($_carte_grise) {
            $_cg = $_carte_grise[0];

            // Visite
            $_visite = $this->getLastVisiteByCarteGrise($_cg);
            $_array_carte_grise['vst_id'] = null;
            if ($_visite)
                $_array_carte_grise['vst_id'] = $_visite[0]->getId();

            // Carte grise
            $_array_carte_grise['cg_date_emission'] = $_cg->getCgDateEmission()->format('d/m/Y');
            $_array_carte_grise['cg_numero_immatriculation'] = $_cg->getCgImmatriculation();
            $_array_carte_grise['cg_nom_proprietaire'] = $_cg->getCgNom();
            $_array_carte_grise['cg_prenom_proprietaire'] = $_cg->getCgPrenom();
            $_array_carte_grise['cg_profession_proprietaire'] = $_cg->getCgProfession();
            $_array_carte_grise['cg_adresse_proprietaire'] = $_cg->getCgAdresse();
            $_array_carte_grise['cg_phone'] = $_cg->getCgPhone();
            $_array_carte_grise['cg_commune_proprietaire'] = $_cg->getCgCommune();
            $_array_carte_grise['cg_puissance_administrative'] = $_cg->getCgPuissanceAdmin();
            $_array_carte_grise['cg_nbr_assis'] = $_cg->getCgNbrAssis();
            $_array_carte_grise['cg_mise_en_service'] = $_cg->getCgMiseEnService() ? $_cg->getCgMiseEnService()->format('d/m/Y') : '';
            $_array_carte_grise['cg_carte_violette'] = $_cg->getCgNumCarteViolette();
            $_array_carte_grise['cg_lieu_carte_violette'] = $_cg->getCgLieuCarteViolette();
            $_array_carte_grise['cg_date_carte_violette'] = $_cg->getCgDateCarteViolette() ? $_cg->getCgDateCarteViolette()->format('d/m/Y') : '';
            $_array_carte_grise['cg_patente'] = $_cg->getCgPatente();
            $_array_carte_grise['cg_ani'] = $_cg->getCgAni();
            $_array_carte_grise['cg_carosserie'] = $_cg->getCtCarosserie()->getId();
            $_array_carte_grise['cg_source'] = $_cg->getCtSourceEnergie()->getId();
            $_array_carte_grise['cg_is_transport'] = $_cg->getCgIsTransport();
            $_array_carte_grise['cg_nom_cooperative'] = $_cg->getCgNomCooperative();
            $_array_carte_grise['cg_itineraire'] = $_cg->getCgItineraire();
            $_array_carte_grise['cg_numero_licence'] = $_cg->getCgNumVignette();

            // Vehicule
            $_array_carte_grise['vhc_marque'] = $_cg->getCtVehicule()->getCtMarque()->getId();
            $_array_carte_grise['vhc_genre'] = $_cg->getCtVehicule()->getCtGenre()->getId();
            $_array_carte_grise['vhc_numero_serie'] = $_cg->getCtVehicule()->getVhcNumSerie();
            $_array_carte_grise['vhc_numero_moteur'] = $_cg->getCtVehicule()->getVhcNumMoteur();
            $_array_carte_grise['vhc_type'] = $_cg->getCtVehicule()->getVhcType();
            $_array_carte_grise['vhc_puissance'] = $_cg->getCtVehicule()->getVhcPuissance();
            $_array_carte_grise['vhc_poids_total'] = $_cg->getCtVehicule()->getVhcPoidsTotalCharge();
            $_array_carte_grise['vhc_poids_vide'] = $_cg->getCtVehicule()->getVhcPoidsVide();
            $_array_carte_grise['vhc_charge_utile'] = $_cg->getCtVehicule()->getVhcChargeUtile();
        }

        return $_array_carte_grise;
    }
}
