<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtReception;

use Ct\Service\MetierManagerBundle\Entity\CtReception;
use Ct\Service\MetierManagerBundle\Entity\CtVehicule;
use Ct\Service\MetierManagerBundle\Utils\PathReportingName;
use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use phpDocumentor\Reflection\Types\Integer;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;

class ServiceMetierCtReception
{
    private $_entity_manager;
    private $_container;

    public function __construct(EntityManager $_entity_manager, Container $_container)
    {
        $this->_entity_manager = $_entity_manager;
        $this->_container      = $_container;
    }

    /**
     * Ajouter un message flash
     * @param string $_type
     * @param string $_message
     * @return mixed
     */
    public function setFlash($_type, $_message) {
        return $this->_container->get('session')->getFlashBag()->add($_type, $_message);
    }

    /**
     * Récuperer le repository Reception
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_RECEPTION);
    }

    /**
     * Tester si CHEF_DE_CENTRE
     * @return integer
     */
    public function isChefDeCentre()
    {
        $_user_connected    = $this->_container->get('security.token_storage')->getToken()->getUser();
        $_role_user         = $_user_connected->getCtRole()->getId();
        if ($_role_user != RoleName::ID_ROLE_ADMIN) {
            return 0;
        }
        return $_user_connected->getCtCentre()->getId();
    }

    /**
     * Tester si SUPERADMIN
     * @return boolean
     */
    public function isSuperAdmin()
    {
        $_user_connected    = $this->_container->get('security.token_storage')->getToken()->getUser();
        $_role_user         = $_user_connected->getCtRole()->getId();
        if ($_role_user != RoleName::ID_ROLE_SUPERADMIN) {
            return false;
        }
        return true;
    }

    /**
     * Récuperer tout les Receptions
     * @return array
     */
    public function getAllCtReception()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les receptions par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtReceptionByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer tout les receptions par centre et ordre
     * @param array $_order
     * @param $_center_id
     * @return array
     */
    public function getAllCtReceptionByCentreOrder($_order, $_center_id)
    {
        if ($_center_id > 0)
            return $this->getRepository()->findBy(array('ctCentre' => $_center_id), $_order);
        return $this->getRepository()->findBy(array(), $_order);
    }

    public function getAllCtReceptionGroupedByNumGroup($_order)
    {
        $_receptions = $this->getAllCtReceptionByOrder($_order);
        $_new_receptions = array();
        foreach ($_receptions as $_key => $_reception) {
            $_new_receptions[$_reception->getRcpNumGroup()][] = $_reception;
        }
        return $_new_receptions;
    }

    public function getAllCtReceptionGroupedByNumGroupAndCentre($_order, $_center_id = 0)
    {
        $_receptions = $this->getAllCtReceptionByCentreOrder($_order, $_center_id);
        $_new_receptions = array();
        foreach ($_receptions as $_key => $_reception) {
            $_new_receptions[$_reception->getRcpNumGroup()][] = $_reception;
        }
        return $_new_receptions;
    }

    /**
     * Récuperer tout les receptions par date
     * @param \DateTime $_date
     * @return array
     */
    public function getAllCtReceptionByDate($_date)
    {
        return $this->getRepository()->findByRcpCreated($_date);
    }

    /**
     * Récuperer tout les receptions par date et type
     * @param \DateTime $_date
     * @param $_type
     * @param int $_centre_id
     * @return array
     */
    public function getAllCtReceptionByDateAndType($_date, $_type, $_centre_id)
    {
        if ($_type == 0) {
            return $this->getRepository()->findByRcpCreated($_date);
        }

        return $this->getRepository()->findBy(array(
            'rcpCreated'      => $_date,
            'ctTypeReception' => $_type,
            'ctCentre'        => $_centre_id
        ));
    }

    /**
     * Récuperer une reception par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtReceptionById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Récuperer une reception par identifiant
     * @param CtReception $_reception
     * @return CtReception
     */
    public function resetVehiculeReception($_reception)
    {
        $_tmp_vehicule = $_reception->getCtVehicule();
        $_vehicule = new CtVehicule();
        $_vehicule->setVhcNumSerie("");
        $_vehicule->setVhcNumMoteur("");
        $_vehicule->setCtGenre($_tmp_vehicule->getCtGenre());
        $_vehicule->setCtMarque($_tmp_vehicule->getCtMarque());
        $_vehicule->setVhcCylindre($_tmp_vehicule->getVhcCylindre());
        $_vehicule->setVhcPuissance($_tmp_vehicule->getVhcPuissance());
        $_vehicule->setVhcChargeUtile($_tmp_vehicule->getVhcChargeUtile());
        $_vehicule->setVhcPoidsVide($_tmp_vehicule->getVhcPoidsVide());
        $_vehicule->setVhcType($_tmp_vehicule->getVhcType());

        $_reception->setCtVehicule($_vehicule);
        $_reception->setRcpImmatriculation("");
        return $_reception;
    }

    public function setNumGroupById($_reception, $_uniqid)
    {
        $_marque = $_reception->getCtVehicule()->getCtMarque()->getMrqLibelle();
        $_proprietaire = $_reception->getRcpProprietaire();
        $_date_creation = $_reception->getRcpCreated();
        $_date_creation = $_date_creation->format('Ymd');

        $_reception_type = $_reception->getCtTypeReception()->getTprcpLibelle();
        $_num_group = $_reception_type . '_' . $_marque . '_' . $_proprietaire . '_' . $_date_creation . '_' . $_uniqid;
        $_num_group = str_replace(' ', '_', $_num_group);
        $_num_group = str_replace('-', '_', $_num_group);
        $_num_group = strtoupper($_num_group);
        $_reception->setRcpNumGroup($_num_group);
        return $_reception;
    }

    /**
     * Enregistrer une Reception
     * @param CtReception $_reception
     * @param string $_action
     * @return boolean
     */
    public function saveCtReception($_reception, $_action)
    {
//        $_date_inconnue = new \DateTime('2011-01-01');
//        if (empty($_reception->getRcpMiseService())) {
//            $_reception->setRcpMiseService($_date_inconnue);
//        }

        if ($_action == 'new') {
            // Récupérer manager
            $_vehicule_manager = $this->_container->get(ServiceName::SRV_METIER_VEHICULE);

            // Tester si le numéro de série voiture existe déjà
            $_numero_serie       = $_reception->getCtVehicule()->getVhcNumSerie();
            $_numero_serie_exist = $_vehicule_manager->findByNumeroSerie($_numero_serie);
            if ($_numero_serie_exist) {
                $_reception->setCtVehicule($_numero_serie_exist[0]);
            }

            $_id_vehicule = $_reception->getCtVehicule()->getId();
            $_marque = $_reception->getCtVehicule()->getCtMarque()->getMrqLibelle();
            $_proprietaire = $_reception->getRcpProprietaire();
            $_date_creation = $_reception->getRcpCreated();
            $_date_creation = $_date_creation->format('Ymd');

            $_reception_type = $_reception->getCtTypeReception()->getTprcpLibelle();
            $_num_group = $_reception_type . '_' . $_marque . '_' . $_proprietaire . '_' . $_date_creation;
            $_num_group = str_replace(' ', '_', $_num_group);
            $_num_group = str_replace('-', '_', $_num_group);
            $_num_group = strtoupper($_num_group);
//            $_reception->setRcpNumGroup($_num_group);

            $this->_entity_manager->persist($_reception);
            $this->_entity_manager->flush();

            $_id_reception = $_reception->getId();

            $_centre_reception = $_reception->getCtCentre();
            $_province = $_centre_reception->getCtProvince();

            $_code_centre = $_centre_reception->getCtrCode();
            $_code_province = $_province->getPrvCode();
            $_type_recep = "RECEP";
            $_current_year = date('Y');

            $_num_pv = $_id_reception . '/CENSERO/' . $_code_province . '/' . $_code_centre . '/' . $_type_recep . '/' . $_current_year;
            $_reception->setRcpNumPv($_num_pv);

            $this->_entity_manager->flush();
            return $_reception;

        } else if ($_action == 'update') {
            // Récupérer manager
            $_vehicule_manager = $this->_container->get(ServiceName::SRV_METIER_VEHICULE);
            // Tester si le numéro de série voiture existe déjà
            $_numero_serie       = $_reception->getCtVehicule()->getVhcNumSerie();
            $_numero_serie_exist = $_vehicule_manager->findByNumeroSerie($_numero_serie);
            if ($_numero_serie_exist) {
                $_reception->setCtVehicule($_numero_serie_exist[0]);
            }

            $_id_vehicule = $_reception->getCtVehicule()->getId();
            $_marque = $_reception->getCtVehicule()->getCtMarque()->getMrqLibelle();
            $_proprietaire = $_reception->getRcpProprietaire();
            $_date_creation = $_reception->getRcpCreated();
            $_date_creation = $_date_creation->format('Ymd');

            $_reception_type = $_reception->getCtTypeReception()->getTprcpLibelle();
            $_num_group = $_reception_type . '_' . $_marque . '_' . $_proprietaire . '_' . $_date_creation;
            $_num_group = str_replace(' ', '_', $_num_group);
            $_num_group = str_replace('-', '_', $_num_group);
            $_num_group = strtoupper($_num_group);
//            $_reception->setRcpNumGroup($_num_group);

            $this->_entity_manager->persist($_reception);
            $this->_entity_manager->flush();

            // Historique modification
            $_historique_manager = $this->_container->get(ServiceName::SRV_METIER_HISTORIQUE);
            $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();
            $_date_time_modif = new \DateTime();
            $_date_time_modif = $_date_time_modif->format("d/m/Y H:i:s");
            $_historique_manager->logActionUser($_user_connected, $_reception, $_date_time_modif);

            $_id_reception = $_reception->getId();


            $_centre_reception = $_reception->getCtCentre();
            $_province = $_centre_reception->getCtProvince();

            $_code_centre = $_centre_reception->getCtrCode();
            $_code_province = $_province->getPrvCode();
            $_type_recep = "RECEP";
            $_current_year = date('Y');

            $_num_pv = $_id_reception . '/CENSERO/' . $_code_province . '/' . $_code_centre . '/' . $_type_recep . '/' . $_current_year;
            $_reception->setRcpNumPv($_num_pv);

            $this->_entity_manager->flush();
            return $_reception;
        }
    }

    public function getCtReceptionByNumGroup($_num_group)
    {
        $_entity_m = EntityName::CT_RECEPTION;
        $_dql    = "SELECT t
                    FROM $_entity_m t 
                    WHERE t.rcpNumGroup = ?1";
        $_query  = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter(1, $_num_group);
        return $_query->getResult();
    }

    public function getCtReceptionByNumSerieVehicule($_num_serie)
    {
        $_entity_r = EntityName::CT_RECEPTION;
        $_dql    = "SELECT r
                    FROM $_entity_r r
                    LEFT JOIN r.ctVehicule v
                    WHERE v.vhcNumSerie LIKE ?1
                    GROUP BY v";
        $_query  = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter(1, $_num_serie . '%');
        $_query->setMaxResults(10);
        $_res = $_query->getResult();

        $_receptions = array();
        foreach ($_res as $_reception) {
            $_receptions[] = $this->getArrayOfReception($_reception);
        }
        return $_receptions;
    }

    public function getCtReceptionByImmatriculeVehicule($_immatricule)
    {
        $_entity_r = EntityName::CT_RECEPTION;
        $_dql    = "SELECT r
                    FROM $_entity_r r
                    WHERE r.rcpImmatriculation LIKE ?1";
        $_query  = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter(1, $_immatricule . '%');
        $_query->setMaxResults(10);
        $_res = $_query->getResult();

        $_receptions = array();
        foreach ($_res as $_reception) {
            $_receptions[] = $this->getArrayOfReception($_reception);
        }
        return $_receptions;
    }

    /**
     * Récuperer une reception
     * @param CtReception $_reception
     * @param bool $_is_search tester si utiliser pour autocomplete
     * @return array
     */
    public function getArrayOfReception($_reception, $_is_search = true)
    {

        $_rcpVehicule                   = $_reception->getCtVehicule();
        
        return array(
            'rcp_id'                    => $_reception->getId(),
            'rcp_mise_service'          => $_is_search ? $_reception->getRcpMiseService()->format('Y') : $_reception->getRcpMiseService(),
            'rcp_immatriculation'       => $_reception->getRcpImmatriculation(),
            'rcp_proprietaire'          => $_reception->getRcpProprietaire(),
            'rcp_profession'            => $_reception->getRcpProfession(),
            'rcp_adresse'               => $_reception->getRcpAdresse(),
            'rcp_nbr_assis'             => $_reception->getRcpNbrAssis(),
            'rcp_nbr_debout'            => $_reception->getRcpNbrDebout(),
            'rcp_num_pv'                => $_reception->getRcpNumPv(),
            'rcp_num_group'             => $_reception->getRcpNumGroup(),
            'rcp_created'               => $_reception->getRcpCreated(),
            'rcp_centre'                => $_is_search ? $_reception->getCtCentre()->getId() : $_reception->getCtCentre(),
            'rcp_province'              => $_is_search ? $_reception->getCtCentre()->getCtProvince()->getId() : $_reception->getCtCentre()->getCtProvince(),
            'rcp_motif'                 => $_is_search ? $_reception->getCtMotif()->getId() : $_reception->getCtMotif(),
            'rcp_reception_type'        => $_is_search ? $_reception->getCtTypeReception()->getTprcpLibelle() : $_reception->getCtTypeReception(),
            'rcp_user'                  => $_is_search ? $_reception->getCtUser()->getId() : $_reception->getCtUser(),
            'rcp_utilisation'           => $_is_search ? $_reception->getCtUtilisation()->getId() : $_reception->getCtUtilisation(),
            'rcp_source_energie'        => $_is_search ? $_reception->getCtSourceEnergie()->getId() : $_reception->getCtSourceEnergie(),
            'rcp_carosserie'            => $_is_search ? $_reception->getCtCarosserie()->getId() : $_reception->getCtCarosserie(),
            'rcp_num_pv_modified'       => str_replace('/', '_', $_reception->getRcpNumPv()),

            'rcp_veh_id'                => $_rcpVehicule->getId(),
            'rcp_veh_type_serie'        => $_rcpVehicule->getVhcType(),
            'rcp_veh_cylindre'          => $_rcpVehicule->getVhcCylindre(),
            'rcp_veh_puissance'         => $_rcpVehicule->getVhcPuissance(),
            'rcp_veh_poids_vide'        => $_rcpVehicule->getVhcPoidsVide(),
            'rcp_veh_charge_utile'      => $_rcpVehicule->getVhcChargeUtile(),
            'rcp_veh_ptac'              => $_rcpVehicule->getVhcPoidsTotalCharge(),
            'rcp_veh_hauteur'           => $_rcpVehicule->getVhcHauteur(),
            'rcp_veh_largeur'           => $_rcpVehicule->getVhcLargeur(),
            'rcp_veh_longueur'          => $_rcpVehicule->getVhcLongueur(),
            'rcp_veh_num_serie'         => $_rcpVehicule->getVhcNumSerie(),
            'rcp_veh_num_moteur'        => $_rcpVehicule->getVhcNumMoteur(),
            'rcp_veh_created'           => $_rcpVehicule->getVhcCreated(),
            'rcp_veh_provenance'        => $_rcpVehicule->getVhcProvenance(),
            'rcp_veh_genre'             => $_is_search ? $_rcpVehicule->getCtGenre()->getId() : $_rcpVehicule->getCtGenre(),
            'rcp_veh_marque'            => $_is_search ? $_rcpVehicule->getCtMarque()->getId() : $_rcpVehicule->getCtMarque(),
        );

    }

    /**
     * Supprimer une Reception
     * @param Entity $_reception
     * @return boolean
     */
    public function deleteCtReception($_reception)
    {
        $this->_entity_manager->remove($_reception);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un Reception
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtReception($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_reception = $this->getCtReceptionById($_id);
                $this->deleteCtReception($_reception);
            }
        }

        return true;
    }

    /**
     * Générer PV Reception
     * @param string $_num_group
     * @return array
     */
    public function generatePVReception($_num_group)
    {
        $_receptions = $this->getCtReceptionByNumGroup($_num_group);
        $_dest_final = null;
        $_taux_tva = $this->_container->getParameter('taxe');
        if (isset($_receptions) && (count($_receptions) > 0)) {
            $_ptac_manager        = $this->_container->get(ServiceName::SRV_METIER_DROIT_PTAC);
            $_pv_manager          = $this->_container->get(ServiceName::SRV_METIER_PROCES_VERBAL);
            $_motif_tarif_manager = $this->_container->get(ServiceName::SRV_METIER_MOTIF_TARIF);

            // Récupérer répertoire modèle Word
            $_pv_directory        = $this->_container->getParameter('reporting_template_directory');
            $_filename            = $_num_group;

            $_path                = $_pv_directory . PathReportingName::GENERATE_RECEPTION;
            $_dest_final          = $_path . $_filename . '.docx';

            $_url_scheme            = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
            $_path_docx             = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_RECEPTION . $_filename . '.docx';
            $_path_pdf              = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_RECEPTION . $_filename . '.pdf';

            $_file_without_ext    = $_filename;

            if (count($_receptions) === 1) { // RECEPTION ISOLE
                $_source_pv         = $_pv_directory . PathReportingName::TEMPLATE_RECEPTION_ISOLE;
                $_phpWord           = new PhpWord();
                $_template          = $_phpWord->loadTemplate($_source_pv);
                $_reception         = $_receptions[0];

                $_num_pv            = $_reception->getRcpNumPv();
                //$_filename          =  str_replace('/', '_', $_reception->getRcpNumPv());

                //$_dest_tmp          = $_pv_directory . PathReportingName::GENERATE_RECEPTION . $_filename . '.docx';
                $_motif             = $_reception->getCtMotif();
                $_reception_type    = $_reception->getCtTypeReception()->getTprcpLibelle();
                $_droit             = null;

                $_charge_utile      = $_reception->getCtVehicule()->getVhcChargeUtile();
                $_poids_vide        = $_reception->getCtVehicule()->getVhcPoidsVide();

                $_centre            = $_reception->getCtCentre()->getCtrNom();
                $_user_nom          = $_reception->getRcpProprietaire();
                $_genre             = $_reception->getCtVehicule()->getCtGenre()->getGrLibelle();
                $_marque            = $_reception->getCtVehicule()->getCtMarque()->getMrqLibelle();
                $_type              = $_reception->getCtVehicule()->getVhcType();
                $_moteur            = $_reception->getCtVehicule()->getVhcNumMoteur();
                $_puissance         = $_reception->getCtVehicule()->getVhcPuissance();
                $_poid_vide         = $_reception->getCtVehicule()->getVhcPoidsVide();
                $_cu                = $_reception->getCtVehicule()->getVhcChargeUtile();
                $_type_serie        = $_reception->getCtVehicule()->getVhcNumSerie();
                $_source            = $_reception->getCtSourceEnergie()->getSreLibelle();
                $_cylindre          = $_reception->getCtVehicule()->getVhcCylindre();
                $_motif_recep       = $_reception->getCtMotif()->getMtfLibelle();
                $_date_mise_en_circ = $_reception->getRcpMiseService() ? $_reception->getRcpMiseService()->format('d/m/Y') : '-';
                $_mise_en_circ      = $_date_mise_en_circ == '01/01/2011' ? '-' : $_date_mise_en_circ;
                $_immatriculation   = $_reception->getRcpImmatriculation();
                $_caross            = $_reception->getCtCarosserie()->getCrsLibelle();
                $_assise            = $_reception->getRcpNbrAssis();
                $_addr              = $_reception->getRcpAdresse();
                $_date              = $_reception->getRcpCreated();
                $_utilisation       = $_reception->getCtUtilisation();
                $_util_libelle      = $_utilisation->getUtLibelle();
                $_is_administratif  = (stripos($_util_libelle, "administratif") !== false) ? true: false;
                
                // Calcul Poids Total en Charge
                if (is_numeric($_charge_utile) && is_numeric($_poids_vide)) {
                    $_ptac          = $_charge_utile + $_poids_vide;
                } else {
                    $_ptac          = 0;
                }

                if ($_motif->getMtfIsCalculable()) { // Calcul droit en fonction de poids                    
                    $_ptac_tonne        = $_ptac / 1000; // Convertir en tonne
                    $_categorie_genre   = $_reception->getCtVehicule()->getCtGenre()->getCtGenreCategorie();
                    $_droit             = $_ptac_manager->getDroitByCategorieGenreEtPtac($_categorie_genre, $_ptac_tonne, 'reception');

                } else {
                    $_droit             = $_motif_tarif_manager->getCtMotifTarifByMotif($_motif);
                }

                if ($_droit) {
                    $_droit_f = number_format($_droit, 0, ',', ' ');
                    if ($_is_administratif) $_template->setValue('motif', "0");
                    else $_template->setValue('motif', $_droit_f);
                }
                
                // Recuperer type PV
                $_str_pv_type = null;
                if (strpos($_reception_type, 'ype') !== false) { // Par type
                    // Valeurs figés (il faut penser à modifier)
                    $_str_pv_type = 'RT_TYPE';
                } elseif (strpos($_reception_type, 'sol') !== false) { // Isole
                    $_str_pv_type = 'RT_ISOLE';
                }

                // Recuperer tarif PV 
                $_pv = null;
                if (isset($_str_pv_type)) {
                    $_pv = $_pv_manager->getCtProcesVerbalByType($_str_pv_type);
                }
                
                // Total hors taxe et TVA
                $_tht = null;
                if (isset($_droit) && isset($_pv)) {
                    $_m_libelle = strtolower($_motif->getMtfLibelle());
                    if (strpos($_m_libelle, 'pesage tous') !== false) { // Faut pas figer la valeur
                        $_pv = 0;
                        $_tva = 0;
                        $_tht = $_droit + $_pv;
                    } else {
                        //$_tva       = $_droit * $_taux_tva;
                        $_tht = $_pv + $_droit;
                        $_tva = $_tht * $_taux_tva;
                    }
                    $_timbre = 0; // Je sais pas la valeur
                    $_montant = $_tht + $_tva + $_timbre;
                    $_tht_f = number_format($_tht, 0, ',', ' ');
                    $_pv_f = number_format($_pv, 0, ',', ' ');
                    $_tva_f = number_format($_tva, 0, ',', ' ');
                    $_timbre_f = number_format($_timbre, 0, ',', ' ');
                    $_montant_f = number_format($_montant, 0, ',', ' ');

                    if ($_is_administratif) {
                        $_template->setValue('tht', 0);
                        $_template->setValue('tva', 0);
                        $_template->setValue('timbres', 0);
                        $_template->setValue('montant', 0);
                        $_template->setValue('prix_pv', 0);
                    } else {
                        $_template->setValue('tht', $_tht_f);
                        $_template->setValue('tva', $_tva_f);
                        $_template->setValue('timbres', $_timbre_f);
                        $_template->setValue('montant', $_montant_f);
                        $_template->setValue('prix_pv', $_pv_f);
                    }
                }

                $_template->setValue('centre', $_centre);
                $_template->setValue('user_nom', htmlspecialchars($_user_nom));
                $_template->setValue('genre', $_genre);
                $_template->setValue('marque', $_marque);
                $_template->setValue('type', $_type);
                $_template->setValue('moteur', $_moteur);
                $_template->setValue('puissance', $_puissance);
                $_template->setValue('poid_vide', $_poid_vide);
                $_template->setValue('cu', $_cu);
                $_template->setValue('type_serie', $_type_serie);
                $_template->setValue('source', $_source);
                $_template->setValue('cylindre', $_cylindre);
                $_template->setValue('motif_recep', $_motif_recep);
                $_template->setValue('mise_en_circ', $_mise_en_circ);
                $_template->setValue('immatriculation', $_immatriculation);
                $_template->setValue('caross', $_caross);
                $_template->setValue('assise', $_assise);
                $_template->setValue('addr', $_addr);
                $_template->setValue('ptc', $_ptac);
                $_template->setValue('ptr', $_ptac);
                $_template->setValue('num_pv', $_num_pv);
                $_template->setValue('date', $_date->format('d/m/Y'));

                $_template->saveAs($_dest_final);
            } else { // PAR TYPE
                $_source_pv         = $_pv_directory . PathReportingName::TEMPLATE_RECEPTION_TYPE;
                $_list_of_files     = array();
                foreach ($_receptions as $_reception) {
                    $_phpWord           = new PhpWord();
                    $_template          = $_phpWord->loadTemplate($_source_pv);

                    $_num_pv            = $_reception->getRcpNumPv();
                    $_filename          =  str_replace('/', '_', $_reception->getRcpNumPv());

                    $_dest_tmp          = $_pv_directory . PathReportingName::GENERATE_RECEPTION . $_filename . '.docx';
                    $_motif             = $_reception->getCtMotif();
                    $_reception_type    = $_reception->getCtTypeReception()->getTprcpLibelle();
                    $_droit             = null;

                    $_charge_utile      = $_reception->getCtVehicule()->getVhcChargeUtile();
                    $_poids_vide        = $_reception->getCtVehicule()->getVhcPoidsVide();

                    $_centre            = $_reception->getCtCentre()->getCtrNom();
                    $_user_nom          = $_reception->getRcpProprietaire();
                    $_genre             = $_reception->getCtVehicule()->getCtGenre()->getGrLibelle();
                    $_marque            = $_reception->getCtVehicule()->getCtMarque()->getMrqLibelle();
                    $_type              = $_reception->getCtVehicule()->getVhcType();
                    $_moteur            = $_reception->getCtVehicule()->getVhcNumMoteur();
                    $_puissance         = $_reception->getCtVehicule()->getVhcPuissance();
                    $_poid_vide         = $_reception->getCtVehicule()->getVhcPoidsVide();
                    $_cu                = $_reception->getCtVehicule()->getVhcChargeUtile();
                    $_type_serie        = $_reception->getCtVehicule()->getVhcNumSerie();
                    $_source            = $_reception->getCtSourceEnergie()->getSreLibelle();
                    $_cylindre          = $_reception->getCtVehicule()->getVhcCylindre();
                    $_motif_recep       = $_reception->getCtMotif()->getMtfLibelle();
                    $_mise_en_circ      = $_reception->getRcpMiseService() ? $_reception->getRcpMiseService()->format('d/m/Y') : '';
                    $_immatriculation   = $_reception->getRcpImmatriculation();
                    $_caross            = $_reception->getCtCarosserie()->getCrsLibelle();
                    $_assise            = $_reception->getRcpNbrAssis();
                    $_addr              = $_reception->getRcpAdresse();
                    $_date              = $_reception->getRcpCreated();
                    $_utilisation       = $_reception->getCtUtilisation();
                    $_util_libelle      = $_utilisation->getUtLibelle();
                    $_is_administratif  = (stripos($_util_libelle, "administratif") !== false) ? true: false;

                    // Calcul Poids Total en Charge
                    if (is_numeric($_charge_utile) && is_numeric($_poids_vide)) {
                        $_ptac          = $_charge_utile + $_poids_vide;
                    } else {
                        $_ptac          = 0;
                    }

                    if ($_motif->getMtfIsCalculable()) { // Calcul droit en fonction de poids
                        $_ptac_tonne        = $_ptac / 1000; // Convertir en tonne
                        $_categorie_genre   = $_reception->getCtVehicule()->getCtGenre()->getCtGenreCategorie();
                        $_droit             = $_ptac_manager->getDroitByCategorieGenreEtPtac($_categorie_genre, $_ptac_tonne, 'reception');
                    } else {
                        $_droit             = $_motif_tarif_manager->getCtMotifTarifByMotif($_motif);
                    }

                    if ($_droit) {
                        $_droit_f = number_format($_droit, 0, ',', ' ');
                        if ($_is_administratif) $_template->setValue('motif', "0");
                        else $_template->setValue('motif', $_droit_f);
                    }

                    // Recuperer type PV
                    $_str_pv_type = null;
                    if (strpos($_reception_type, 'ype') !== false) { // Par type
                        // Valeurs figés (il faut penser à modifier)
                        $_str_pv_type = 'RT_TYPE';
                    } elseif (strpos($_reception_type, 'sol') !== false) { // Isole
                        $_str_pv_type = 'RT_ISOLE';
                    }

                    // Recuperer tarif PV
                    $_pv = null;
                    if (isset($_str_pv_type)) {
                        $_pv = $_pv_manager->getCtProcesVerbalByType($_str_pv_type);
                    }

                    // Total hors taxe et TVA
                    $_tht = null;
                    if (isset($_droit) && isset($_pv)) {
                        $_m_libelle = strtolower($_motif->getMtfLibelle());
                        if (strpos($_m_libelle, 'pesage tous') !== false) { // Faut pas figer la valeur
                            $_pv        = 0;
                            $_tva       = 0;
                            $_tht       = $_droit + $_pv;
                        } else {
                            //$_tva       = $_droit * $_taux_tva;
                            $_tht       = $_pv + $_droit;
                            $_tva       = $_tht * $_taux_tva;
                        }
                        $_timbre  = 0; // Je sais pas la valeur
                        $_montant = $_tht + $_tva + $_timbre;

                        $_tht_f = number_format($_tht, 0, ',', ' ');
                        $_pv_f = number_format($_pv, 0, ',', ' ');
                        $_tva_f = number_format($_tva, 0, ',', ' ');
                        $_timbre_f = number_format($_timbre, 0, ',', ' ');
                        $_montant_f = number_format($_montant, 0, ',', ' ');

                        if ($_is_administratif) {
                            $_template->setValue('tht', 0);
                            $_template->setValue('tva', 0);
                            $_template->setValue('timbres', 0);
                            $_template->setValue('montant', 0);
                            $_template->setValue('prix_pv', 0);
                        } else {
                            $_template->setValue('tht', $_tht_f);
                            $_template->setValue('tva', $_tva_f);
                            $_template->setValue('timbres', $_timbre_f);
                            $_template->setValue('montant', $_montant_f);
                            $_template->setValue('prix_pv', $_pv_f);
                        }
                    }

                    $_template->setValue('centre', $_centre);
                    $_template->setValue('user_nom', $_user_nom);
                    $_template->setValue('genre', $_genre);
                    $_template->setValue('marque', $_marque);
                    $_template->setValue('type', $_type);
                    $_template->setValue('moteur', $_moteur);
                    $_template->setValue('puissance', $_puissance);
                    $_template->setValue('poid_vide', $_poid_vide);
                    $_template->setValue('cu', $_cu);
                    $_template->setValue('type_serie', $_type_serie);
                    $_template->setValue('source', $_source);
                    $_template->setValue('cylindre', $_cylindre);
                    $_template->setValue('motif_recep', $_motif_recep);
                    $_template->setValue('mise_en_circ', $_mise_en_circ);
                    $_template->setValue('immatriculation', $_immatriculation);
                    $_template->setValue('caross', $_caross);
                    $_template->setValue('assise', $_assise);
                    $_template->setValue('addr', $_addr);
                    $_template->setValue('ptc', $_ptac);
                    $_template->setValue('ptr', $_ptac);
                    $_template->setValue('num_pv', $_num_pv);
                    $_template->setValue('date', $_date->format('d/m/Y'));
                    $_template->saveAs($_dest_tmp);
                    $_list_of_files[] = $_dest_tmp;
                }
                // Combiner les fichiers en un seul document
                $_zip = new \clsTbsZip();
                $content1 = null;
                if (count($_list_of_files) > 0) {
                    $_file_1 = $_list_of_files[0];
                    $_zip->Open($_file_1);
                    $content1 = $_zip->FileRead('word/document.xml');
                    $_zip->Close();
                    $p = strpos($content1, '<w:body');
                    if ($p===false) exit("Tag <w:body> not found in document 1.");
                    $p = strpos($content1, '>', $p);
                    $content1 = substr($content1, $p+1);
                    $p = strpos($content1, '</w:body>');
                    if ($p===false) exit("Tag </w:body> not found in document 1.");
                    $content1 = substr($content1, 0, $p);

                    // Supprimer fichier temporaire
                    if (file_exists($_file_1)) {
                        unlink($_file_1);
                    }

                    for ($_index = 1; $_index < count($_list_of_files) - 1; $_index++) {
                        $_file_2 = $_list_of_files[$_index];
                        $_zip->Open($_file_2);
                        $content2 = $_zip->FileRead('word/document.xml');
                        $p = strpos($content2, '<w:body>');
                        if ($p===false) exit("Tag <w:body> not found in document 2.");
                        $p = strpos($content2, '>', $p);
                        $content2 = substr($content2, $p+1);
                        $p = strpos($content2, '</w:body>');
                        if ($p===false) exit("Tag </w:body> not found in document 2.");
                        $content2 = substr($content2, 0, $p);
                        $content2 = substr_replace($content2, $content1, $p, 0);
                        $content1 = $content2;
                        if (file_exists($_file_2)) {
                            unlink($_file_2);
                        }
                    }

                    $_file_2 = $_list_of_files[count($_list_of_files) - 1];
                    $_zip->Open($_file_2);
                    $content2 = $_zip->FileRead('word/document.xml');
                    $p = strpos($content2, '</w:body>');
                    if ($p===false) exit("Tag </w:body> not found in document 2.");
                    $content2 = substr_replace($content2, $content1, $p, 0);
                    $_zip->FileReplace('word/document.xml', $content2, TBSZIP_STRING);
                    $_zip->Flush(TBSZIP_FILE, $_dest_final);
                    if (file_exists($_file_2)) {
                        unlink($_file_2);
                    }
                }

            }
            // Recuperer manager
            $_const_av_ded_manager = $this->_container->get(ServiceName::SRV_METIER_CONST_AV_DED);
            // Convertir en PDF
            $_dest_final = $_const_av_ded_manager->convertToPdf($_path, $_file_without_ext);

            return array(
                'download_path' => $_dest_final,
                'url_path'      => $_path_pdf
            );

        }
    }

    /**
     * Générer PV Reception lien de telechargement
     * @param integer $_num_group
     * @param string $_ext
     * @return array
     */
    public function generatePVReceptionDownloadLink($_num_group, $_ext)
    {
        $_filename = $_num_group . $_ext;
        $_path = $this->_container->get('kernel')->getRootDir(). "/../web/reporting/" . PathReportingName::GENERATE_RECEPTION;
        return array(
            'filename' => $_filename,
            'path' => $_path
        );

    }

    /**
     * Générer PV Duplicata
     * @param Integer $_reception_id
     * @return string
     */
    public function generatePVDuplicata($_reception_id)
    {
        $_taux_tva = $this->_container->getParameter('taxe');
        $_ptac_manager        = $this->_container->get(ServiceName::SRV_METIER_DROIT_PTAC);
        $_pv_manager          = $this->_container->get(ServiceName::SRV_METIER_PROCES_VERBAL);
        $_motif_tarif_manager = $this->_container->get(ServiceName::SRV_METIER_MOTIF_TARIF);

        $_reception = $this->getCtReceptionById($_reception_id);
        if (isset($_reception)) {
            $_arr_of_rec = $this->getArrayOfReception($_reception, false);

            $_reception_type = $_reception->getCtTypeReception()->getTprcpLibelle();

            // Récupérer répertoire modèle Word
            $_pv_directory          = $this->_container->getParameter('reporting_template_directory');
            $_source_pv             = $_pv_directory . PathReportingName::TEMPLATE_DUPLICATA_ISOLE;
            if (stripos($_reception_type, 'type') !== false) {
                $_source_pv             = $_pv_directory . PathReportingName::TEMPLATE_DUPLICATA_TYPE;
            }
            $_phpWord               = new PhpWord();
            $_template              = $_phpWord->loadTemplate($_source_pv);
            $_filename              =  str_replace('/', '_', $_arr_of_rec['rcp_num_pv']);
            $_path                  = $_pv_directory . PathReportingName::GENERATE_DUPLICATA;
            $_dest_tmp              = $_path . $_filename . '.docx';
            $_file_without_ext      = $_filename;

            $_url_scheme            = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
            $_path_docx             = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_DUPLICATA . $_filename . '.docx';
            $_path_pdf              = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_DUPLICATA . $_filename . '.pdf';

            $_file_without_ext    = $_filename;

            $_motif             = $_reception->getCtMotif();
            $_reception_type    = $_reception->getCtTypeReception()->getTprcpLibelle();
            $_droit             = null;

            $_charge_utile      = $_reception->getCtVehicule()->getVhcChargeUtile();
            $_poids_vide        = $_reception->getCtVehicule()->getVhcPoidsVide();
            $_poid_vide         = $_reception->getCtVehicule()->getVhcPoidsVide();
            $_cu                = $_reception->getCtVehicule()->getVhcChargeUtile();
            $_utilisation       = $_reception->getCtUtilisation();
            $_util_libelle      = $_utilisation->getUtLibelle();
            $_is_administratif  = (stripos($_util_libelle, "administratif") !== false) ? true: false;

            // Calcul Poids Total en Charge
            if (is_numeric($_charge_utile) && is_numeric($_poids_vide)) {
                $_ptac          = $_charge_utile + $_poids_vide;
            } else {
                $_ptac          = 0;
            }

            if ($_motif->getMtfIsCalculable()) { // Calcul droit en fonction de poids
                $_ptac_tonne        = $_ptac / 1000; // Convertir en tonne
                $_categorie_genre   = $_reception->getCtVehicule()->getCtGenre()->getCtGenreCategorie();
                $_droit             = $_ptac_manager->getDroitByCategorieGenreEtPtac($_categorie_genre, $_ptac_tonne, 'reception');
            } else {
                $_droit             = $_motif_tarif_manager->getCtMotifTarifByMotif($_motif);
            }

            if ($_droit) {
                $_droit_f = number_format($_droit, 0, ',', ' ');
                if ($_is_administratif) $_template->setValue('motif_recep', "0");
                else $_template->setValue('motif_recep', $_droit_f);
            }

            // Recuperer type PV
            $_str_pv_type = null;
            if (strpos($_reception_type, 'ype') !== false) { // Par type
                // Valeurs figés (il faut penser à modifier)
                $_str_pv_type = 'RT_TYPE';
            } elseif (strpos($_reception_type, 'sol') !== false) { // Isole
                $_str_pv_type = 'RT_ISOLE';
            }

            // Recuperer tarif PV
            $_pv = null;
            if (isset($_str_pv_type)) {
                $_pv = $_pv_manager->getCtProcesVerbalByType($_str_pv_type);
            }

            // Total hors taxe et TVA
            $_tht = null;
            if (isset($_droit) && isset($_pv)) {
                $_m_libelle = strtolower($_motif->getMtfLibelle());
                if (strpos($_m_libelle, 'pesage tous') !== false) { // Faut pas figer la valeur
                    $_pv        = 0;
                    $_tva       = 0;
                    $_tht       = $_droit + $_pv;
                } else {
                    //$_tva       = $_droit * $_taux_tva;
                    $_tht       = $_pv + $_droit;
                    $_tva       = $_tht * $_taux_tva;
                }
                $_timbre  = 0; // Je sais pas la valeur
                $_montant = $_tht + $_tva + $_timbre;

                $_tht_f = number_format($_tht, 0, ',', ' ');
                $_pv_f = number_format($_pv, 0, ',', ' ');
                $_tva_f = number_format($_tva, 0, ',', ' ');
                $_timbre_f = number_format($_timbre, 0, ',', ' ');
                $_montant_f = number_format($_montant, 0, ',', ' ');

                if ($_is_administratif) {
                    $_template->setValue('tht', 0);
                    $_template->setValue('tva', 0);
                    $_template->setValue('timbres', 0);
                    $_template->setValue('montant', 0);
                    $_template->setValue('prix_pv', 0);
                } else {
                    $_template->setValue('tht', $_tht_f);
                    $_template->setValue('tva', $_tva_f);
                    $_template->setValue('timbres', $_timbre_f);
                    $_template->setValue('montant', $_montant_f);
                    $_template->setValue('prix_pv', $_pv_f);
                }
            }

            $_template->setValue('addr', htmlspecialchars($_arr_of_rec['rcp_adresse']));
            $_template->setValue('centre', $_arr_of_rec['rcp_centre']->getCtrNom());
            $_template->setValue('mise_en_circ', $_arr_of_rec['rcp_mise_service']->format('d/m/Y'));
            $_template->setValue('user_nom', htmlspecialchars($_arr_of_rec['rcp_proprietaire']));

            $_template->setValue('immatriculation', $_arr_of_rec['rcp_immatriculation']);
            $_template->setValue('assise', $_arr_of_rec['rcp_nbr_assis']);
            $_template->setValue('num_pv', $_arr_of_rec['rcp_num_pv']);
            $_template->setValue('date', $_arr_of_rec['rcp_created']->format('d/m/Y'));
            $_template->setValue('motif_recep', $_arr_of_rec['rcp_motif']->getMtfLibelle());
            $_template->setValue('source', $_arr_of_rec['rcp_source_energie']->getSreLibelle());
            $_template->setValue('caross', $_arr_of_rec['rcp_carosserie']->getCrsLibelle());
            $_template->setValue('type', $_arr_of_rec['rcp_veh_type_serie']);
            $_template->setValue('cylindre', $_arr_of_rec['rcp_veh_cylindre']);
            $_template->setValue('puissance', $_arr_of_rec['rcp_veh_puissance']);
            $_template->setValue('poid_vide', $_arr_of_rec['rcp_veh_poids_vide']);
            $_template->setValue('cu', $_arr_of_rec['rcp_veh_charge_utile']);
            $_template->setValue('ptc', $_arr_of_rec['rcp_veh_ptac']);
            $_template->setValue('ptr', $_arr_of_rec['rcp_veh_ptac']);
            $_template->setValue('type_serie', $_arr_of_rec['rcp_veh_num_serie']);
            $_template->setValue('moteur', $_arr_of_rec['rcp_veh_num_moteur']);
            $_template->setValue('genre', $_arr_of_rec['rcp_veh_genre']->getGrLibelle());
            $_template->setValue('marque', $_arr_of_rec['rcp_veh_marque']->getMrqLibelle());
            $_template->setValue('date_duplicata', date('d/m/Y'));

            $_template->saveAs($_dest_tmp);

            // Recuperer manager
            $_const_av_ded_manager = $this->_container->get(ServiceName::SRV_METIER_CONST_AV_DED);
            // Convertir en PDF
            $_dest_tmp = $_const_av_ded_manager->convertToPdf($_path, $_file_without_ext);

            return array(
                'download_path' => $_dest_tmp,
                'url_path'      => $_path_pdf
            );

        }

    }

    /**
     * Générer PV Duplicata lien de telechargement
     * @param integer $_num_group
     * @param string $_num_pv
     * @return array
     */
    public function generatePVDuplicataDownloadLink($_num_pv, $_ext)
    {
        $_num_pv   =  str_replace('/', '_', $_num_pv);
        $_filename = $_num_pv . $_ext;
        $_path = $this->_container->get('kernel')->getRootDir(). "/../web/reporting/" . PathReportingName::GENERATE_DUPLICATA;
        return array(
            'filename' => $_filename,
            'path' => $_path
        );

    }

    /**
     * Générer Feuille de caisse journaliere
     * @param \DateTime $_date
     * @param $_type
     * @return array
     */
    public function generateFeuilleDeCaisse($_date, $_type)
    {
        // Recuperer managers
        $_ptac_manager          = $this->_container->get(ServiceName::SRV_METIER_DROIT_PTAC);
        $_pv_manager            = $this->_container->get(ServiceName::SRV_METIER_PROCES_VERBAL);
        $_motif_tarif_manager   = $this->_container->get(ServiceName::SRV_METIER_MOTIF_TARIF);

        $_user_connected        = $this->_container->get('security.token_storage')->getToken()->getUser();
        $_centre                = $_user_connected->getCtCentre();
        $_receptions            = $this->getAllCtReceptionByDateAndType($_date, $_type, $_centre->getId());
        $_code_centre           = $_centre->getCtrCode();
        $_nom_centre            = $_centre->getCtrNom();
        $_province              = $_centre->getCtProvince();
        $_code_province         = $_province->getPrvCode();
        $_nb_reception          = count($_receptions);

        $_pv_directory          = $this->_container->getParameter('reporting_template_directory');
        if ($_type == 1) { // par type
            $_source_template       = $_pv_directory . PathReportingName::TEMPLATE_REC_FEUILLE_DE_CAISSE_TYPE;
        } elseif ($_type == 2) { // isole
            $_source_template       = $_pv_directory . PathReportingName::TEMPLATE_REC_FEUILLE_DE_CAISSE_ISOLE;
        } else {
            $_source_template       = $_pv_directory . PathReportingName::TEMPLATE_REC_FEUILLE_DE_CAISSE;
        }


        $_phpWord               = new PhpWord();
        $_template              = $_phpWord->loadTemplate($_source_template);
        $_today                 = date('dmY');

        $_num_fdc               = $_today . '_FDC_' . $_code_province . '_' . $_code_centre . '_RECEPTION';
        $_filename              = $_num_fdc;
        $_path                  = $_pv_directory . PathReportingName::GENERATE_REC_FEUILLE_DE_CAISSE;
        $_dest_tmp              = $_path . $_filename . '.docx';
        $_file_without_ext      = $_filename;

        $_url_scheme            = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path_docx             = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_REC_FEUILLE_DE_CAISSE . $_filename . '.docx';
        $_path_pdf              = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_REC_FEUILLE_DE_CAISSE . $_filename . '.pdf';

        // Centre et date
        $_template->setValue('centre', $_nom_centre);
        $_template->setValue('date', $_date->format('d/m/Y'));


        $_template->cloneRow('ncontrole', count($_receptions));

        $_total_droit = 0;
        $_total_pv = 0;
        $_total_tva = 0;
        $_montant_total = 0;
        foreach ($_receptions as $_key => $_reception) {
            ++$_key;

            $_reception_type    = $_reception->getctTypeReception();
            $_rec_type_label    = $_reception_type->getTprcpLibelle();
            $_num_pv            = $_reception->getRcpNumPv();
            $_motif             = $_reception->getCtMotif();
            $_motif_label       = $_motif->getMtfLibelle();
            $_imm               = $_reception->getRcpImmatriculation();
            $_utilisation       = $_reception->getCtUtilisation();
            $_util_label        = $_utilisation->getUtLibelle();
            $_is_administratif  = (stripos($_util_label, "administratif") !== false) ? true: false;

            $_vehicule          = $_reception->getCtVehicule();
            $_genre             = $_vehicule->getCtGenre();
            $_genre_label       = $_genre->getGrLibelle();
            $_ptac              = $_vehicule->getVhcPoidsTotalCharge();

            /**
             * DEBUT CALCUL MONTANT
             */
            if ($_motif->getMtfIsCalculable()) { // Calcul droit en fonction de poids
                $_ptac_tonne        = $_ptac / 1000; // Convertir en tonne
                $_categorie_genre   = $_genre->getCtGenreCategorie();
                $_droit             = $_ptac_manager->getDroitByCategorieGenreEtPtac($_categorie_genre, $_ptac_tonne, 'reception');
            } else {
                $_droit             = $_motif_tarif_manager->getCtMotifTarifByMotif($_motif);
            }
            // Recuperer type PV
            $_str_pv_type = null;
            if (strpos($_rec_type_label, 'ype') !== false) { // Par type
                // Valeurs figés (il faut penser à modifier)
                $_str_pv_type = 'RT_TYPE';
            } elseif (strpos($_rec_type_label, 'sol') !== false) { // Isole
                $_str_pv_type = 'RT_ISOLE';
            }

            // Recuperer tarif PV
            $_pv = null;
            if (isset($_str_pv_type)) {
                $_pv = $_pv_manager->getCtProcesVerbalByType($_str_pv_type);
            }

            // Total hors taxe et TVA
            $_tht = null;
            $_montant = null;
            $_taux_tva = $this->_container->getParameter('taxe');
            $_tva = null;
            $_timbre  = 0; // Je sais pas la valeur
            if (isset($_droit) && isset($_pv)) {
                $_m_libelle = strtolower($_motif->getMtfLibelle());
                if (strpos($_m_libelle, 'pesage tous') !== false) { // Faut pas figer la valeur
                    $_pv  = 0;
                    $_tht = $_droit + $_pv;
                    $_tva = $_tht * $_taux_tva;
                } else {
                    $_tht = $_pv + $_droit;
                    $_tva = $_tht * $_taux_tva;
                }
                $_montant = $_tht + $_tva + $_timbre;
            }

            if ($_is_administratif) {
                $_droit = 0;
                $_pv = 0;
                $_tva = 0;
                $_timbre = 0;
                $_montant = 0;
            }

            $_droit_f = number_format($_droit, 0, ',', ' ');
            $_pv_f = number_format($_pv, 0, ',', ' ');
            $_tva_f = number_format($_tva, 0, ',', ' ');
            $_timbre_f = number_format($_timbre, 0, ',', ' ');
            $_montant_f = number_format($_montant, 0, ',', ' ');

            $_genre_reception = $_reception->getCtGenre() ? $_reception->getCtGenre()->getGrLibelle() : $_genre_label;

            $_template->setValue('ncontrole#' . $_key, $_num_pv);
            $_template->setValue('motif#' . $_key, $_motif_label);
            $_template->setValue('genre#' . $_key, $_genre_reception);
            $_template->setValue('imm#' . $_key, $_imm);

            $_template->setValue('droit#' . $_key, $_droit_f);
            $_template->setValue('pv#' . $_key, $_pv_f);
            $_template->setValue('tva#' . $_key, $_tva_f);
            $_template->setValue('timbre#' . $_key, $_timbre_f);
            $_template->setValue('montant#' . $_key, $_montant_f);
            $_template->setValue('utilisation#' . $_key, $_util_label);

            $_total_droit += $_droit;
            $_total_pv += $_pv;
            $_total_tva += $_tva;
            $_montant_total += $_montant;
        }
        $_total_droit_f = number_format($_total_droit, 0, ',', ' ');
        $_total_pv_f = number_format($_total_pv, 0, ',', ' ');
        $_total_tva_f = number_format($_total_tva, 0, ',', ' ');
        $_montant_total_f = number_format($_montant_total, 0, ',', ' ');

        $_template->setValue('nb_controle', $_nb_reception);
        $_template->setValue('droit_total', $_total_droit_f);
        $_template->setValue('pv_total', $_total_pv_f);
        $_template->setValue('total_tva', $_total_tva_f);
        $_template->setValue('montant_total', $_montant_total_f);
        $_template->saveAs($_dest_tmp);

        // Recuperer manager
        $_const_av_ded_manager = $this->_container->get(ServiceName::SRV_METIER_CONST_AV_DED);
        // Convertir en PDF
        $_dest_tmp = $_const_av_ded_manager->convertToPdf($_path, $_file_without_ext);

        return array(
            'download_path' => $_dest_tmp,
            'url_path'      => $_path_pdf
        );
    }

    /**
     * Générer Fiche de controle Reception
     * @param \DateTime $_date
     * @param integer $_type
     * @return array
     */
    public function generateFicheDeControle($_date, $_type)
    {
        $_user_connected        = $this->_container->get('security.token_storage')->getToken()->getUser();
        $_centre                = $_user_connected->getCtCentre();
        $_receptions            = $this->getAllCtReceptionByDateAndType($_date, $_type, $_centre->getId());
        $_code_centre           = $_centre->getCtrCode();
        $_nom_centre            = $_centre->getCtrNom();
        $_province              = $_centre->getCtProvince();
        $_code_province         = $_province->getPrvCode();
        $_nom_province          = $_province->getPrvNom();
        $_nb_reception          = count($_receptions);

        $_pv_directory          = $this->_container->getParameter('reporting_template_directory');

        if ($_type == 1) { // par type
            $_source_template       = $_pv_directory . PathReportingName::TEMPLATE_REC_FICHE_DE_CONTROLE_TYPE;
        } elseif ($_type == 2) { // isole
            $_source_template       = $_pv_directory . PathReportingName::TEMPLATE_REC_FICHE_DE_CONTROLE_ISOLE;
        } else {
            $_source_template       = $_pv_directory . PathReportingName::TEMPLATE_REC_FICHE_DE_CONTROLE;
        }

        $_phpWord               = new PhpWord();
        $_template              = $_phpWord->loadTemplate($_source_template);
        $_today                 = date('dmY');

        $_num_fdc               = $_today . '_FDCTRL_' . $_code_province . '_' . $_code_centre . '_RECEPTION';
        $_filename              = $_num_fdc;
        $_path                  = $_pv_directory . PathReportingName::GENERATE_REC_FICHE_DE_CONTROLE;
        $_dest_tmp              = $_path . $_filename . '.docx';
        $_file_without_ext      = $_filename;

        $_url_scheme            = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path_docx             = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_REC_FICHE_DE_CONTROLE . $_filename . '.docx';
        $_path_pdf              = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_REC_FICHE_DE_CONTROLE . $_filename . '.pdf';


        // Centre, Province et date
        $_template->setValue('centre', $_nom_centre);
        $_template->setValue('province', $_nom_province);
        $_template->setValue('date', $_date->format('d/m/Y'));


        $_template->cloneRow('proprietaire', $_nb_reception);
        foreach ($_receptions as $_key => $_reception) {
            ++$_key;
            $_proprietaire          = $_reception->getRcpProprietaire();
            $_adresse               = $_reception->getRcpAdresse();

            $_vehicule              = $_reception->getCtVehicule();
            $_marque                = $_vehicule->getCtMarque()->getMrqLibelle();
            $_provenance            = $_vehicule->getVhcProvenance();
            $_immatriculation       = $_reception->getRcpImmatriculation();

            $_template->setValue('proprietaire#' . $_key, htmlspecialchars($_proprietaire));
            $_template->setValue('adresse#' . $_key, htmlspecialchars($_adresse));
            $_template->setValue('marque#' . $_key, $_marque);
            $_template->setValue('provenance#' . $_key, $_provenance);
            $_template->setValue('imma#' . $_key, $_immatriculation);
        }
        $_template->saveAs($_dest_tmp);

        // Recuperer manager
        $_const_av_ded_manager = $this->_container->get(ServiceName::SRV_METIER_CONST_AV_DED);
        // Convertir en PDF
        $_dest_tmp = $_const_av_ded_manager->convertToPdf($_path, $_file_without_ext);

        return array(
            'download_path' => $_dest_tmp,
            'url_path'      => $_path_pdf
        );
    }

    /**
     * Récupérer liste json numéro d'immatriculation
     * @param string $_term
     * @return array
     */
    public function getListNumeroImmatriculation($_term)
    {
        $_entity = EntityName::CT_RECEPTION;
        $_dql    = "SELECT r.rcpImmatriculation FROM $_entity r
                    WHERE r.rcpImmatriculation LIKE :term";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('term', $_term . '%');
        $_query->setMaxResults(10);

        $_results = $_query->getResult();

        // Traitement réponse Json
        $_numero_immatriculations = array();
        foreach ($_results as $_result) {
            $_numero_immatriculations[] = $_result['rcpImmatriculation'];
        }

        $_response = new JsonResponse();
        $_response->setData($_numero_immatriculations);

        return $_response;
    }

    /**
     * Récupérer liste json numéro de série vehicule
     * @param string $_term
     * @return array
     */
    public function getListNumeroSerie($_term)
    {
        $_entity = EntityName::CT_RECEPTION;
        $_dql    = "SELECT v.vhcNumSerie FROM $_entity r
                    LEFT JOIN r.ctVehicule v
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
     * Récupérer l'information réception technique par numéro
     * @param string $_numero
     * @param string $_type
     * @return array
     */
    public function getInfoByNumero($_numero, $_type)
    {
        $_where_type = 'r.rcpImmatriculation = :numero';
        if ($_type == 'serie')
            $_where_type = 'v.vhcNumSerie = :numero';

        $_entity = EntityName::CT_RECEPTION;
        $_dql    = "SELECT r FROM $_entity r
                    LEFT JOIN r.ctVehicule v
                    WHERE $_where_type
                    ORDER BY r.id ASC";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('numero', $_numero);

        return $_query->getResult();
    }
}
