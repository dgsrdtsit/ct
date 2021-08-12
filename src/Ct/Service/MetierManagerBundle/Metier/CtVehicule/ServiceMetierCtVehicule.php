<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtVehicule;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;

class ServiceMetierCtVehicule
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
     * Récuperer le repository véhicule
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_VEHICULE);
    }

    /**
     * Récuperer tout les véhicules
     * @return array
     */
    public function getAllCtVehicule()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les véhicules par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtVehiculeByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un véhicule par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtVehiculeById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Modifier un véhicule
     * @param Entity $_vehicule
     * @return boolean
     */
    public function updateCtVehicule($_vehicule)
    {
        $this->saveCtVehicule($_vehicule, 'update');
    }

    /**
     * Enregistrer un véhicule
     * @param Entity $_vehicule
     * @param string $_action
     * @return boolean
     */
    public function saveCtVehicule($_vehicule, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_vehicule);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un véhicule
     * @param Entity $_vehicule
     * @return boolean
     */
    public function deleteCtVehicule($_vehicule)
    {
        $this->_entity_manager->remove($_vehicule);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un véhicule
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtVehicule($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_vehicule = $this->getCtVehiculeById($_id);
                $this->deleteCtVehicule($_vehicule);
            }
        }

        return true;
    }

    /**
     * Récupérer un véhicule par numéro de série
     * @param string $_numero_serie
     * @return array
     */
    public function findByNumeroSerie($_numero_serie)
    {
        return $this->getRepository()->findByVhcNumSerie($_numero_serie);
    }

    /**
     * Récupérer l'information véhicule par numéro de série
     * @param string $_numero_serie
     * @return array
     */
    public function getArrayInfoVehiculeByNumeroSerie($_numero_serie)
    {
        $_vehicule = $this->getRepository()->findByVhcNumSerie($_numero_serie);

        // Traitement tableau
        $_array_vehicule = array();
        if ($_vehicule) {
            $_array_vehicule['vhc_marque'] = $_vehicule[0]->getCtMarque()->getId();
            $_array_vehicule['vhc_genre'] = $_vehicule[0]->getCtGenre()->getId();
            $_array_vehicule['vhc_numero_serie'] = $_vehicule[0]->getVhcNumSerie();
            $_array_vehicule['vhc_numero_moteur'] = $_vehicule[0]->getVhcNumMoteur();
            $_array_vehicule['vhc_type'] = $_vehicule[0]->getVhcType();
            $_array_vehicule['vhc_puissance'] = $_vehicule[0]->getVhcPuissance();
            $_array_vehicule['vhc_poids_vide'] = $_vehicule[0]->getVhcPoidsVide();
            $_array_vehicule['vhc_charge_utile'] = $_vehicule[0]->getVhcChargeUtile();
            $_array_vehicule['vhc_ptac'] = $_vehicule[0]->getVhcPoidsTotalCharge();
            $_array_vehicule['vhc_carosserie'] = $_vehicule[0]->getVhcPoidsTotalCharge();

        }

        return $_array_vehicule;
    }

    /**
     * Récupérer liste numéro de série
     * @param string $_term
     * @return array
     */
    public function getListNumeroSerie($_term)
    {
        $_entity = EntityName::CT_VEHICULE;
        $_dql = "SELECT v.vhcNumSerie FROM $_entity v
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

    public function getVehiculeInfoFromReception($_numero_serie)
    {
        $_vehicule = $this->getRepository()->findByVhcNumSerie($_numero_serie);

        $_array_vehicule = array();
        if (isset($_vehicule[0])) {
            $_vehicule = $_vehicule[0];

            $_entity_r = EntityName::CT_RECEPTION;
            $_dql = "SELECT r FROM $_entity_r r
                        LEFT JOIN r.ctCarosserie car
                        LEFT JOIN r.ctSourceEnergie enr          
                        WHERE r.ctVehicule = :vehicule_id";

            $_query = $this->_entity_manager->createQuery($_dql);

            $_query->setParameter('vehicule_id', $_vehicule->getId());

//            print_r($_query->getSQL());die;
            $_reception = $_query->getSingleResult();
            $_car = $_reception->getCtCarosserie();
            $_source = $_reception->getCtSourceEnergie();
            $_mise_service = $_reception->getRcpMiseService()->format('d/m/Y');
            $_nb_assis = $_reception->getRcpNbrAssis();

            $_array_vehicule['vhc_marque'] = $_vehicule->getCtMarque()->getId();
            $_array_vehicule['vhc_genre'] = $_vehicule->getCtGenre()->getId();
            $_array_vehicule['vhc_numero_serie'] = $_vehicule->getVhcNumSerie();
            $_array_vehicule['vhc_numero_moteur'] = $_vehicule->getVhcNumMoteur();
            $_array_vehicule['vhc_type'] = $_vehicule->getVhcType();
            $_array_vehicule['vhc_puissance'] = $_vehicule->getVhcPuissance();
            $_array_vehicule['vhc_poids_vide'] = $_vehicule->getVhcPoidsVide();
            $_array_vehicule['vhc_charge_utile'] = $_vehicule->getVhcChargeUtile();
            $_array_vehicule['vhc_ptac'] = $_vehicule->getVhcPoidsTotalCharge();
            $_array_vehicule['vhc_carosserie'] = $_car->getId();
            $_array_vehicule['vhc_source'] = $_source->getId();
            $_array_vehicule['vhc_mise_service'] = $_mise_service;
            $_array_vehicule['vhc_nbr_assis'] = $_nb_assis;

        }

        return $_array_vehicule;
    }
}
