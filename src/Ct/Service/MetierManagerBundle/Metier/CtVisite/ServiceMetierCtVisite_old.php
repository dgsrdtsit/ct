<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtVisite;

use Ct\Service\MetierManagerBundle\Entity\CtVisite;
use Ct\Service\MetierManagerBundle\Utils\PathReportingName;
use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtVisite
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
     * Récuperer le repository visite
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_VISITE);
    }

    /**
     * Récuperer tout les visites
     * @return array
     */
    public function getAllCtVisite()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les visites par centre
     * @return array
     */
    public function getAllCtVisiteByCentreAndDate($_centre_id, $_date, $_visite_type_id, $_aptitude = "ALL")
    {
        if ($_aptitude == "APTE") {
            $_and_stmt = "AND v.vstIsApte = 1 ";
        } elseif ($_aptitude == "INAPTE") {
            $_and_stmt = "AND v.vstIsApte = 0 ";
        } else {
            $_and_stmt = "";
        }
        if ($_visite_type_id > 0) {
            $_join_stmt = "LEFT JOIN v.ctTypeVisite tv ";
            $_and_stmt .= "AND tv.id = :type_visite";
        } else {
            $_join_stmt = "";
        }

        $_entity_v = EntityName::CT_VISITE;
        $_dql = "SELECT v FROM $_entity_v v
                 LEFT JOIN v.ctCentre c
                 LEFT JOIN v.ctCarteGrise cg
                 $_join_stmt
                 WHERE c.id = :id
                 AND v.vstCreated LIKE :date
                 $_and_stmt
                 AND cg.cgImmatriculation IS NOT NULL
                 ORDER BY v.vstCreated DESC";
        $_query     = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('id', $_centre_id);
        $_query->setParameter('date', '%'.$_date.'%');
        if ($_visite_type_id > 0) {
            $_query->setParameter('type_visite', $_visite_type_id);
        }
        return $_query->getResult();
    }

    /**
     * Récuperer tout les anomalies par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtVisiteByOrder($_order)
    {
        return $this->getRepository()->findBy(
            array('vstIsApte' => 0, 'vstIsContreVisite' => 0),
            $_order
        );
    }

    /**
     * Récuperer tout les visites par centre et ordre
     * @param array $_order
     * @param $_center_id
     * @return array
     */
    public function getAllCtVisiteByCentreOrder($_order, $_center_id)
    {
        if ($_center_id > 0)
            return $this->getRepository()->findBy(
                array('ctCentre' => $_center_id, 'vstIsApte' => 0, 'vstIsContreVisite' => 0),
                $_order
            );
        return $this->getAllCtVisiteByOrder($_order);
    }

    /**
     * Récuperer une visite par id
     * @param Integer $_id
     * @return array
     */
    public function getCtVisiteById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Liste visite par verificateur
     * @param integer $_verificateur_id
     * @param \DateTime $_date
     * @return array
     */
    public function getCtVisiteByVerificateur($_verificateur_id, $_date = 0)
    {
        $_and_stmt = "";
        if ($_date != 0) {
            $_and_stmt = "AND v.vstCreated LIKE :date ";
        }
        $_entity_v  = EntityName::CT_VISITE;
        $_dql       = "SELECT v
                      FROM $_entity_v v
                      LEFT JOIN v.ctVerificateur verif
                      WHERE verif.id = ?1
                      $_and_stmt
                      ORDER BY v.vstCreated DESC";
        $_query     = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter(1, $_verificateur_id);
        if ($_date != 0) {
            $_query->setParameter('date', '%'.$_date.'%');
        }
        return $_query->getResult();
    }

    /**
     * Vérifier si la voiture a déjà fait un visite dans la même journée
     * @param string $_immatriculation
     * @return boolean
     */
    public function isVisiteExist($_immatriculation)
    {
        $_visite = EntityName::CT_VISITE;
        $_dql    = "SELECT vst
                    FROM $_visite vst
                    LEFT JOIN vst.ctCarteGrise cg
                    WHERE vst.vstCreated LIKE :now
                    AND cg.cgImmatriculation = :immatriculation";

        $_date = (new \DateTime())->format('Y-m-d');
        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('now', '%' . $_date . '%');
        $_query->setParameter('immatriculation', $_immatriculation);

        $_result = $_query->getResult();

        if (count($_result) > 0)
            return true;

        return false;
    }

    /**
     * Ajouter un visite
     * @param CtVisite $_visite
     * @return boolean
     */
    public function addCtVisite(CtVisite $_visite)
    {
        // Vérifier si la voiture a déjà fait un visite dans la même journée
        $_carte_grise     = $_visite->getCtCarteGrise()->getCgImmatriculation();
        $_is_visite_exist = $this->isVisiteExist($_carte_grise);
        if ($_is_visite_exist)
            return false;

        // Récupérer manager
        $_carte_grise_manager = $this->_container->get(ServiceName::SRV_METIER_CARTE_GRISE);

        // Récupérer l'utilisateur connecté
        $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();

        // Traitement centre
        if ($_user_connected->getCtRole()->getId() != RoleName::ID_ROLE_SUPERADMIN) {
            $_user_centre = $_user_connected->getCtCentre();
            $_visite->setCtCentre($_user_centre);
        }

        $_visite->setCtUser($_user_connected);

        // Tester si le numéro d'immatriculation carte grise existe déjà
        $_immatriculation       = $_visite->getCtCarteGrise()->getCgImmatriculation();
        $_immatriculation_exist = $_carte_grise_manager->findByImmatriculation($_immatriculation);
        if ($_immatriculation_exist) {
            $_visite->setCtCarteGrise($_immatriculation_exist[0]);
        }

        // Traitement aptitude
        $_anomalie = count($_visite->getCtAnomalie());
        $_visite->setVstIsApte(0);
        if ($_anomalie == 0)
            $_visite->setVstIsApte(1);

        $this->saveCtVisite($_visite, 'new');

        // Génération numéro PV
        $_id_visite     = $_visite->getId();
        $_code_centre   = $_visite->getCtCentre()->getCtrCode();
        $_code_province = $_visite->getCtCentre()->getCtProvince()->getPrvCode();
        $_type_visite   = $_visite->getCtTypeVisite()->getTpvLibelle();
        $_current_year  = date('Y');
        $_type_visite   =  str_replace(' ', '_', $_type_visite);
        $_num_pv        = $_id_visite . '/' . $_code_province . '/' . $_code_centre . '/' . $_type_visite . '/' . $_current_year;
        $_visite->setVstNumPv($_num_pv);

        $this->saveCtVisite($_visite, 'update');

        return true;
    }

    /**
     * Ajouter un contre visite
     * @param CtVisite $_visite
     * @return boolean
     */
    public function addCtContreVisite(CtVisite $_visite)
    {
        // Récupérer manager
        $_carte_grise_manager = $this->_container->get(ServiceName::SRV_METIER_CARTE_GRISE);

        // Récupérer l'utilisateur connecté
        $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();

        // Traitement centre
        if ($_user_connected->getCtRole()->getId() != RoleName::ID_ROLE_SUPERADMIN) {
            $_user_centre = $_user_connected->getCtCentre();
            $_visite->setCtCentre($_user_centre);
        }

        $_visite->setCtUser($_user_connected);


        // Tester si le numéro d'immatriculation carte grise existe déjà
        $_immatriculation       = $_visite->getCtCarteGrise()->getCgImmatriculation();
        $_immatriculation_exist = $_carte_grise_manager->findByImmatriculation($_immatriculation);
        if ($_immatriculation_exist) {
            $_visite->setCtCarteGrise($_immatriculation_exist[0]);
        }

        // Traitement aptitude
        $_anomalie = count($_visite->getCtAnomalie());
        $_visite->setVstIsApte(0);
        if ($_anomalie == 0) {
            $_visite->setVstIsApte(1);
            $_visite->setVstIsContreVisite(1);
        }

        $this->saveCtVisite($_visite, 'new');

        // Génération numéro PV
        $_id_visite     = $_visite->getId();
        $_code_centre   = $_visite->getCtCentre()->getCtrCode();
        $_code_province = $_visite->getCtCentre()->getCtProvince()->getPrvCode();
        $_type_visite   = $_visite->getCtTypeVisite()->getTpvLibelle();
        $_current_year  = date('Y');
        $_num_pv        = $_id_visite . '/' . $_code_province . '/' . $_code_centre . '/' . $_type_visite . '/' . $_current_year;
        $_visite->setVstNumPv($_num_pv);

        $this->saveCtVisite($_visite, 'update');
        return $_id_visite;
    }

    /**
     * Modifier un visite
     * @param CtVisite $_visite
     * @return boolean
     */
    public function updateCtVisite(CtVisite $_visite)
    {
        // Récupérer le centre de l'utilisateur connecté
        $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();

        $_visite->setCtUser($_user_connected);
        $_visite->setVstUpdated(new \DateTime());

        // Traitement aptitude
        $_anomalie = count($_visite->getCtAnomalie());
        $_visite->setVstIsApte(0);
        if ($_anomalie == 0)
            $_visite->setVstIsApte(1);

        $this->saveCtVisite($_visite, 'modif');
    }

    /**
     * Modifier un contre visite
     * @param CtVisite $_visite
     * @return boolean
     */
    public function updateCtContreVisite($_visite)
    {
        // Récupérer le centre de l'utilisateur connecté
        $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();

        $_visite->setCtUser($_user_connected);

        $_visite->setVstUpdated(new \DateTime());

        // Traitement aptitude
        $_anomalie = count($_visite->getCtAnomalie());
        $_visite->setVstIsApte(0);
        if ($_anomalie == 0) {
            $_visite->setVstIsApte(1);
            $_visite->setVstIsContreVisite(1);
        }

        $this->saveCtVisite($_visite, 'new');
    }

    /**
     * Enregistrer un visite
     * @param CtVisite $_visite
     * @param string $_action
     * @return boolean
     */
    public function saveCtVisite(CtVisite $_visite, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_visite);
        }
        if ($_action == 'modif') {
            // Historique modification
            $_historique_manager = $this->_container->get(ServiceName::SRV_METIER_HISTORIQUE);
            $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();
            $_date_time_modif = new \DateTime();
            $_date_time_modif = $_date_time_modif->format("d/m/Y H:i:s");
            $_historique_manager->logActionUser($_user_connected, $_visite, $_date_time_modif);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un visite
     * @param CtVisite $_visite
     * @return boolean
     */
    public function deleteCtVisite(CtVisite $_visite)
    {
        $this->_entity_manager->remove($_visite);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un visite
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtVisite($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_visite = $this->getCtVisiteById($_id);
                $this->deleteCtVisite($_visite);
            }
        }

        return true;
    }

    /**
     * Générer un reporting carte visite
     * @param CtVisite $_visite
     * @return string
     */
    public function generateReporting(CtVisite $_visite) {
        // Récupérer répertoire modèle Word
        $_reporting_directory   = $this->_container->getParameter('reporting_template_directory');
        $_template_carte_visite = $_reporting_directory . PathReportingName::TEMPLATE_VISITE_CARTE_VISITE;
        $_num_pv                = $_visite->getVstNumPv();
        $_num_pv_upper          = strtoupper(str_replace('/', '_', $_num_pv));
        $_filename              = str_replace(' ', '_', $_num_pv_upper);

        $_path                  = $_reporting_directory . PathReportingName::GENERATE_VISITE_CARTE_VISITE;
        $_dest_tmp              = $_path . $_filename . '.docx';
        $_file_without_ext      = $_filename;

        $_url_scheme            = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path_docx             = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_CARTE_VISITE . $_filename . '.docx';
        $_path_pdf              = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_CARTE_VISITE . $_filename . '.pdf';


        $_php_word = new PhpWord();
        $_template = $_php_word->loadTemplate($_template_carte_visite);

        // Récupération informations
        $_usage                  = $_visite->getCtUsage()->getUsgLibelle();
        $_numero_immatriculation = $_visite->getCtCarteGrise()->getCgImmatriculation();
        $_nom                    = $_visite->getCtCarteGrise()->getCgNom();
        $_prenom                 = $_visite->getCtCarteGrise()->getCgPrenom();
        $_adresse                = $_visite->getCtCarteGrise()->getCgAdresse();
        $_nom_adresse            = $_nom . ' ' . $_prenom . ' ' . $_adresse;
        $_commune                = $_visite->getCtCarteGrise()->getCgCommune();
        $_marque                 = $_visite->getCtCarteGrise()->getCtVehicule()->getCtMarque()->getMrqLibelle();
        $_type                   = $_visite->getCtCarteGrise()->getCtVehicule()->getVhcType();
        $_numero_serie           = $_visite->getCtCarteGrise()->getCtVehicule()->getVhcNumSerie();
        $_numero_moteur          = $_visite->getCtCarteGrise()->getCtVehicule()->getVhcNumMoteur();
        $_carrosserie            = $_visite->getCtCarteGrise()->getCtCarosserie()->getCrsLibelle();
        $_date_mise_circulation  = $_visite->getCtCarteGrise()->getCgMiseEnService();
        $_annee_mise_circulation = $_date_mise_circulation ? $_date_mise_circulation->format('Y') : '-';
        $_poids_vide             = $_visite->getCtCarteGrise()->getCtVehicule()->getVhcPoidsVide();
        $_charge_utile           = $_visite->getCtCarteGrise()->getCtVehicule()->getVhcChargeUtile();
        $_poids_total_charge     = $_visite->getCtCarteGrise()->getCtVehicule()->getVhcPoidsTotalCharge();
        $_date_visite            = $_visite->getVstCreated()->format('d/m/Y');
        $_date_expiration        = $_visite->getVstDateExpiration()->format('d/m/Y');

        // Récupération alpha et titre
        $_option          = array();
        $_option['alpha'] = '';
        $_option['titre'] = '';

        // Usage effectif VP
        if ($_usage == 'VP') {
            $_option['alpha'] = 'C';
            $_option['titre'] = 'VEHICULE PLUS de 5 ans Moins de 3 500 Kg';
        }

        // Usage effectif TM3 REM3
        $_array_tm_rem = array('TM 3 ( Age = 5 ans)', 'TM 3 ( Age >5 ans)', 'REM3');
        if (in_array($_usage, $_array_tm_rem)) {
            $_option['alpha'] = 'E';
            $_option['titre'] = 'TRANSPORT de MARCHANDISE Moins de 3 500 Kg';
        }

        // Usage effectif AE
        $_array_ae = array('AE ( Age = 10 ans)', 'AE ( Age > 10ans)');
        if (in_array($_usage, $_array_ae)) {
            $_option['alpha'] = 'F';
            $_option['titre'] = 'AUTO - ECOLE';
        }

        // Usage effectif TM6 TM7 TM10 TM16 - REM6 REM7 REM10 REM16 - SREM6 SREM7 SREM10 SREM16
        $_array_tm_srem = array('TM6', 'TM7', 'TM10', 'TM 16', 'REM6', 'REM7', 'REM 10', 'REM 16',
            'SREM6', 'SREM7', 'SREM 10', 'SREM 16');
        if (in_array($_usage, $_array_tm_srem)) {
            $_option['alpha'] = 'E';
            $_option['titre'] = 'TRANSPORT de MARCHANDISE 3 500 Kg  par PTC';
        }

        // Usage effectif TCP
        if ($_usage == 'TPP') {
            $_option['alpha'] = 'C';
            $_option['titre'] = 'Véhicule de Transport moins 3 500 Kg effectué au transport de personne';
        }

        $_template->setValue('alpha', htmlspecialchars($_option['alpha']));
        $_template->setValue('titre', htmlspecialchars($_option['titre']));
        $_template->setValue('numero_immatriculation', htmlspecialchars($_numero_immatriculation));
        $_template->setValue('nom_adresse', htmlspecialchars($_nom_adresse));
        $_template->setValue('commune', htmlspecialchars($_commune));
        $_template->setValue('marque',htmlspecialchars( $_marque));
        $_template->setValue('type', htmlspecialchars($_type));
        $_template->setValue('numero_serie', htmlspecialchars($_numero_serie));
        $_template->setValue('numero_moteur', htmlspecialchars($_numero_moteur));
        $_template->setValue('carrosserie', htmlspecialchars($_carrosserie));
        $_template->setValue('annee_mise_circulation', htmlspecialchars($_annee_mise_circulation));
        $_template->setValue('patc', htmlspecialchars($_poids_total_charge));
        $_template->setValue('pav', htmlspecialchars($_poids_vide));
        $_template->setValue('cu', htmlspecialchars($_charge_utile));
        $_template->setValue('date_visite', htmlspecialchars($_date_visite));
        $_template->setValue('date_expiration', htmlspecialchars($_date_expiration));

        $_template->saveAs($_dest_tmp);

        // Recuperer manager
        $_const_av_ded_manager = $this->_container->get(ServiceName::SRV_METIER_CONST_AV_DED);

        // Convertir en PDF
        $_dest_tmp = $_const_av_ded_manager->convertToPdf($_path, $_file_without_ext);

        return array(
            'download_path' => $_dest_tmp,
            'url_path'      => $_path_pdf
        );

//        return $_path;
    }

    /**
     * Récupérer liste numéro de série vehicule
     * @return array
     */
    public function getAllNumeroSerie()
    {
        $_entity = EntityName::CT_VISITE;
        $_dql    = "SELECT vh.vhcNumSerie FROM $_entity v
                    LEFT JOIN v.ctCarteGrise cg
                    LEFT JOIN cg.ctVehicule vh
                    WHERE vh.vhcNumSerie IS NOT NULL
                    GROUP BY vh.vhcNumSerie";

        $_query = $this->_entity_manager->createQuery($_dql);

        return $_query->getResult();
    }

    public function getAllNumeroSerieContreVisite()
    {
        $_entity = EntityName::CT_VISITE;
        $_dql    = "SELECT vh.vhcNumSerie FROM $_entity v
                    LEFT JOIN v.ctCarteGrise cg
                    LEFT JOIN cg.ctVehicule vh
                    WHERE vh.vhcNumSerie IS NOT NULL
                    AND (SELECT MAX(v2.vstCreated) FROM  $_entity v2
                    LEFT JOIN v2.ctCarteGrise cg2
                    WHERE cg2.id = cg.id) = v.vstCreated
                    AND v.vstIsApte = 0
                    AND v.vstIsContreVisite = 0
                    GROUP BY vh.vhcNumSerie";

        $_query = $this->_entity_manager->createQuery($_dql);

        return $_query->getResult();
    }

    /**
     * Récupérer liste numéro d'immatriculation
     * @return array
     */
    public function getAllNumeroImmatriculation()
    {
        $_entity = EntityName::CT_VISITE;
        $_dql    = "SELECT cg.cgImmatriculation FROM $_entity v 
                    LEFT JOIN v.ctCarteGrise cg
                    WHERE cg.cgImmatriculation IS NOT NULL
                    GROUP BY cg.cgImmatriculation";

        $_query = $this->_entity_manager->createQuery($_dql);

        return $_query->getResult();
    }

    /**
     * Récupérer liste numéro d'immatriculation
     * @return array
     */
    public function getAllNumeroImmatriculationContreVisite()
    {
        $_entity = EntityName::CT_VISITE;
        $_dql    = "SELECT cg.cgImmatriculation FROM $_entity v 
                    LEFT JOIN v.ctCarteGrise cg
                    WHERE cg.cgImmatriculation IS NOT NULL
                    AND (SELECT MAX(v2.vstCreated) FROM  $_entity v2
                    LEFT JOIN v2.ctCarteGrise cg2
                    WHERE cg2.id = cg.id) = v.vstCreated
                    AND v.vstIsApte = 0
                    AND v.vstIsContreVisite = 0
                    GROUP BY cg.cgImmatriculation";

        $_query = $this->_entity_manager->createQuery($_dql);

        return $_query->getResult();
    }

    /**
     * Récupérer liste Contre Visite
     * @return array
     */
    public function getAllCtContreVisite($_order, $_centre_id)
    {
        if ($_centre_id > 0) {
            $_join_stmt = "LEFT JOIN v.ctCentre ct ";
            $_and_stmt  = "AND ct.id = :centre_id ";
        } else {
            $_join_stmt = "";
            $_and_stmt  = "";
        }
        if (is_array($_order)) {
            $_k = key($_order);
            $_v = $_order[$_k];
            $_order_stmt = "ORDER BY v." . $_k . " " . $_v;
        } else {
            $_order_stmt = "";
        }
        $_entity = EntityName::CT_VISITE;
        $_dql    = "SELECT v FROM $_entity v 
                    LEFT JOIN v.ctCarteGrise cg
                    $_join_stmt
                    WHERE cg.cgImmatriculation IS NOT NULL
                    $_and_stmt
                    AND (SELECT MAX(v2.vstCreated) FROM  $_entity v2
                    LEFT JOIN v2.ctCarteGrise cg2
                    WHERE cg2.id = cg.id) = v.vstCreated
                    AND v.vstIsApte = 0
                    AND v.vstIsContreVisite = 1
                    $_order_stmt";

        $_query = $this->_entity_manager->createQuery($_dql);
        if ($_centre_id > 0) {
            $_query->setParameter('centre_id', $_centre_id);
        }

        return $_query->getResult();
    }

    /**
     * Récuperer tout les contres visites par centre et ordre
     * @param array $_order
     * @param $_center_id
     * @return array
     */
    public function getAllCtContreVisiteByCentreOrder($_order, $_center_id)
    {
        if ($_center_id > 0)
            return $this->getRepository()->findBy(
                array('ctCentre' => $_center_id, 'vstIsApte' => 0, 'vstIsContreVisite' => 0),
                $_order
            );
        return $this->getAllCtVisiteByOrder($_order);
    }

    /**
     * Récuperer les visites en journalier
     * @param \DateTime $_date
     * @return array
     */
    public function getAllVisiteByDate($_date)
    {
        $_entity = EntityName::CT_VISITE;
        $_dql    = "SELECT v FROM $_entity v 
                    WHERE v.vstCreated LIKE :date";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('date', '%' . $_date . '%');

        return $_query->getResult();
    }

    /**
     * Récupérer un feuille de caisse
     * @param string $_date
     * @return string
     */
    public function getFeuilleCaisse($_date, $_centre_id, $_visite_type_id) {
        // Récupérer manager
        $_usage_tarif_manager        = $this->_container->get(ServiceName::SRV_METIER_USAGE_TARIF);
        $_visite_extra_tarif_manager = $this->_container->get(ServiceName::SRV_METIER_VISITE_EXTRA_TARIF);
        $_pv_manager                 = $this->_container->get(ServiceName::SRV_METIER_PROCES_VERBAL);
        $_centre_manager             = $this->_container->get(ServiceName::SRV_METIER_CENTRE);

        if ($_centre_id > 0) {
            $_centre = $_centre_manager->getCtCentreById($_centre_id);
        } else {
            // Récupérer le centre de l'utilisateur connecté
            $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();
            $_centre         = $_user_connected->getCtCentre();
        }
        $_centre_id = $_centre->getId();

        // Récupération informations
        // Centre et province
        $_nom_centre    = $_centre->getCtrNom();
        $_code_centre   = $_centre->getCtrCode();
        $_nom_province  = $_centre->getCtProvince()->getPrvNom();
        $_code_province = $_centre->getCtProvince()->getPrvCode();

        $_is_omavet = false;

        if (stripos($_nom_centre, "nanisana") !== false) {
            $_is_omavet = true;
        }

        // Visites
        $_visites = $this->getAllCtVisiteByCentreAndDate($_centre_id, $_date, $_visite_type_id);
        $_data = [];
        foreach ($_visites as $_key => $_visite) {
            $_carte_grise  = $_visite->getCtCarteGrise();
            $_usage        = $_visite->getCtUsage();
            if ($_visite_type_id != '')
                $_usage_tarif  = $_usage_tarif_manager->getCtUsageTarifByUsageAndTypeVisite($_usage->getId(), $_visite_type_id)['usgTrfPrix'];
            else
                $_usage_tarif  = $_usage_tarif_manager->getRepository()->findByCtUsage($_usage->getId())[0]->getUsgTrfPrix();
            $_verificateur = $_visite->getCtVerificateur();
            $_extras       = $_visite->getCtVisiteExtra();
            $_type_visite  = $_visite->getCtTypeVisite();

            $_date_created           = $_visite->getVstCreated();
            $_numero_pv              = $_visite->getVstNumPv();
            $_numero_immatriculation = $_carte_grise->getCgImmatriculation();
            $_usage_effectif         = htmlspecialchars($_usage->getUsgLibelle());
            $_nom_verificateur       = $_verificateur->getUsrName();
            $_nom_cooperative        = $_visite->getCtCarteGrise()->getCgNomCooperative();

            $_utilisation      = $_visite->getCtUtilisation();
            $_util_libelle     = $_utilisation->getUtLibelle();
            $_is_administratif = (stripos($_util_libelle, "administratif") !== false) ? true: false;

            $_droit = 0;
            if ($_usage_tarif)
                $_droit = $_usage_tarif;
            $_taxe = 0;
            $_vtb  = 0;

            // PV
            // Si à domicile
            if (stripos($_type_visite->getTpvLibelle(), 'domicile') !== false) {
                $_proces_verbal = $_pv_manager->getRepository()->findByPvType('VT_DOM');
            } elseif (stripos($_type_visite->getTpvLibelle(), 'site') !== false) {
                // Si inapte
                $_proces_verbal = $_pv_manager->getRepository()->findByPvType('VT_INAPTE');

                // Si apte
                if ($_visite->getVstIsApte() == 1)
                    $_proces_verbal = $_pv_manager->getRepository()->findByPvType('VT_APTE');
            }
            $_pv = $_proces_verbal[0]->getPvTarif();

            // Carnet et carte
            $_carnet = 0;
            $_carte  = 0;
            if (count($_extras) > 0) {
                foreach ($_extras as $_extra){
                    $_id_extra = $_extra->getId();

                    // Si carnet
                    if (stripos($_extra->getVsteLibelle(), 'carnet') !== false) {
                        $_visite_extra_tarif = $_visite_extra_tarif_manager->getRepository()->findByCtVisiteExtra(1);
                        if ($_visite_extra_tarif)
                            $_carnet = $_visite_extra_tarif[0]->getVetPrix();
                    }

                    // Si carte
                    if (stripos($_extra->getVsteLibelle(), 'carte') !== false) {
                        $_visite_extra_tarif = $_visite_extra_tarif_manager->getRepository()->findByCtVisiteExtra(2);
                        if ($_visite_extra_tarif)
                            $_carte = $_visite_extra_tarif[0]->getVetPrix();
                    }
                }
            }

            // Taxe
            $_taxe = ($_droit + $_pv + $_carnet + $_carte) * $this->_container->getParameter('taxe');

            // Aptitude
            $_is_apte  = $_visite->getVstIsApte();
            $_aptitude = 'INAPTE';
            if ($_is_apte == 1)
                $_aptitude = 'APTE';

            if ($_is_administratif) {
                $_droit  = 0;
                $_pv     = 0;
                $_carnet = 0;
                $_carte  = 0;
                $_taxe   = 0;
                $_vtb    = 0;
            }

            // Total montant horizontal
            $_total_montant_horizontal = $_droit + $_pv + $_carnet + $_carte + $_taxe + $_vtb;

            // Vérification si le dernier précédent du visite est inapte
            $_is_inapte_last_visite = $this->isInapteLastVisite($_date_created, $_numero_immatriculation);

            if ((($_droit > 0 && $_pv > 0) || $_is_administratif) && !$_is_inapte_last_visite) {
                $_data[$_key]['is_inapte_last_visite'] = $_is_inapte_last_visite;
                $_data[$_key]['date'] = $_date_created->format('d/m/Y');
                $_data[$_key]['ncontrole'] = $_numero_pv;
                $_data[$_key]['imma'] = $_numero_immatriculation;
                $_data[$_key]['usage'] = $_usage_effectif;
                $_data[$_key]['aptitude'] = $_aptitude;
                $_data[$_key]['verif'] = $_nom_verificateur;
                $_data[$_key]['nom_cooperative'] = $_nom_cooperative;
                $_data[$_key]['droit'] = $_droit;
                $_data[$_key]['pv'] = $_pv;
                $_data[$_key]['carnet'] = $_carnet;
                $_data[$_key]['carte'] = $_carte;
                $_data[$_key]['taxe'] = $_taxe;
                $_data[$_key]['vtb'] = $_vtb;
                $_data[$_key]['montant'] = $_total_montant_horizontal;

                if ($_is_omavet) {
                    $_data[$_key]['date_omavet'] = $_date_created->format('d/m/Y');
                    $_data[$_key]['ncontrole_omavet'] = $_numero_pv;
                    $_data[$_key]['imma_omavet'] = $_numero_immatriculation;
                    $_data[$_key]['usage_omavet'] = $_usage_effectif;
                    $_data[$_key]['droit_omavet'] = $_droit;
                }
            }
        }

        return $_data;
    }

    /**
     * Générer un feuille de caisse
     * @param string $_date
     * @return string
     */
    public function generateFeuilleCaisse($_date, $_centre_id, $_visite_type_id) {
        // Récupérer manager
        $_centre_manager = $this->_container->get(ServiceName::SRV_METIER_CENTRE);

        if ($_centre_id > 0) {
            $_centre = $_centre_manager->getCtCentreById($_centre_id);
        } else {
            // Récupérer le centre de l'utilisateur connecté
            $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();
            $_centre         = $_user_connected->getCtCentre();
        }
        $_centre_id = $_centre->getId();

        // Récupération informations
        // Centre et province
        $_nom_centre    = $_centre->getCtrNom();
        $_code_centre   = $_centre->getCtrCode();
        $_nom_province  = $_centre->getCtProvince()->getPrvNom();
        $_code_province = $_centre->getCtProvince()->getPrvCode();

        $_is_omavet = false;

        if (stripos($_nom_centre, "nanisana") !== false) {
            $_is_omavet = true;
        }

        // Récupérer répertoire modèle Word
        $_pv_directory = $this->_container->getParameter('reporting_template_directory');
        $_template_src = $_pv_directory . PathReportingName::TEMPLATE_VISITE_FEUILLE_CAISSE;
        $_date_parsed  = str_replace('-', '_', $_date);
        $_path         = $_pv_directory . PathReportingName::GENERATE_VISITE_FEUILLE_CAISSE;
        if ($_is_omavet) {
            $_template_omavet_src     = $_pv_directory . PathReportingName::TEMPLATE_VISITE_FEUILLE_CAISSE_OMAVET;
            $_num_pv_omavet           = 'feuille_de_caisse_omavet_' . $_date_parsed;
            $_filename_omavet         = strtoupper($_num_pv_omavet);
            $_dest_tmp_omavet         = $_path . $_filename_omavet . '.docx';
            $_file_without_ext_omavet = $_filename_omavet;

            $_url_scheme       = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
            $_path_omavet_docx = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_FEUILLE_CAISSE . $_filename_omavet . '.docx';
            $_path_omavet_pdf  = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_FEUILLE_CAISSE . $_filename_omavet . '.pdf';
        }

        $_num_pv   = 'feuille_de_caisse_' . $_code_centre . '_' . $_code_province . '_' . $_date_parsed;
        $_filename = strtoupper($_num_pv);

        $_dest_tmp         = $_path . $_filename . '.docx';
        $_file_without_ext = $_filename;

        $_url_scheme = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path_docx  = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_FEUILLE_CAISSE . $_filename . '.docx';
        $_path_pdf   = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_FEUILLE_CAISSE . $_filename . '.pdf';

        $_php_word = new PhpWord();
        $_template = $_php_word->loadTemplate($_template_src);

        // Centre et province
        $_template->setValue('province', $_nom_province);
        $_template->setValue('centre', $_nom_centre);

        if ($_is_omavet) {
            $_template_omavet = $_php_word->loadTemplate($_template_omavet_src);
            // Centre et province
            $_template_omavet->setValue('province', $_nom_province);
            $_template_omavet->setValue('centre', $_nom_centre);
        }

        // Visites
        $_visites       = $this->getFeuilleCaisse($_date, $_centre_id, $_visite_type_id);
        $_nbr_visite    = count($_visites);
        $_total_droit   = 0;
        $_total_pv      = 0;
        $_total_carnet  = 0;
        $_total_carte   = 0;
        $_total_taxe    = 0;
        $_total_montant = 0;
        $_template->cloneRow('date', $_nbr_visite);
        if ($_is_omavet) {
            $_dt = new \DateTime($_date);
            $_date = $_dt->format('d/m/Y');
            $_template_omavet->setValue('date', $_date);
            $_template_omavet->cloneRow('date_omavet', $_nbr_visite);
        }
        $_key = 0;
        foreach ($_visites as $_visite) {
            ++$_key;

            // Total montant horizontal
            $_droit = $_visite['droit'];
            $_pv = $_visite['pv'];
            $_carnet = $_visite['carnet'];
            $_carte = $_visite['carte'];
            $_taxe = $_visite['taxe'];
            $_vtb = $_visite['vtb'];
            $_total_montant_horizontal = $_droit + $_pv + $_carnet + $_carte + $_taxe + $_vtb;

            // Total montant vertical
            $_total_droit   += $_droit;
            $_total_pv      += $_pv;
            $_total_carnet  += $_carnet;
            $_total_carte   += $_carte;
            $_total_taxe    += $_taxe;
            $_total_montant += $_total_montant_horizontal;

            $_droit_f                    = number_format($_droit, 0, ',', ' ');
            $_pv_f                       = number_format($_pv, 0, ',', ' ');
            $_carnet_f                   = number_format($_carnet, 0, ',', ' ');
            $_carte_f                    = number_format($_carte, 0, ',', ' ');
            $_taxe_f                     = number_format($_taxe, 0, ',', ' ');
            $_total_montant_horizontal_f = number_format($_total_montant_horizontal, 0, ',', ' ');

            $_template->setValue('date#' . $_key, $_visite['date']);
            $_template->setValue('ncontrole#' . $_key, $_visite['ncontrole']);
            $_template->setValue('imma#' . $_key, $_visite['imma']);
            $_template->setValue('usage#' . $_key, $_visite['usage']);
            $_template->setValue('aptitude#' . $_key, $_visite['aptitude']);
            $_template->setValue('verif#' . $_key, $_visite['verif']);
            $_template->setValue('nom_cooperative#' . $_key, htmlspecialchars($_visite['nom_cooperative']));
            $_template->setValue('droit#' . $_key, $_droit_f);
            $_template->setValue('pv#' . $_key, $_pv_f);
            $_template->setValue('carnet#' . $_key, $_carnet_f);
            $_template->setValue('carte#' . $_key, $_carte_f);
            $_template->setValue('taxe#' . $_key, $_taxe_f);
            $_template->setValue('vtb#' . $_key, $_visite['vtb']);
            $_template->setValue('montant#' . $_key, $_total_montant_horizontal_f);

            if ($_is_omavet) {
                $_template_omavet->setValue('date_omavet#' . $_key, $_visite['date_omavet']);
                $_template_omavet->setValue('ncontrole_omavet#' . $_key, $_visite['ncontrole_omavet']);
                $_template_omavet->setValue('imma_omavet#' . $_key, $_visite['imma_omavet']);
                $_template_omavet->setValue('usage_omavet#' . $_key, $_visite['usage_omavet']);
                $_template_omavet->setValue('droit_omavet#' . $_key, $_droit_f);
            }
        }

        $_total_droit_f   = number_format($_total_droit, 0, ',', ' ');
        $_total_pv_f      = number_format($_total_pv, 0, ',', ' ');
        $_total_carnet_f  = number_format($_total_carnet, 0, ',', ' ');
        $_total_carte_f   = number_format($_total_carte, 0, ',', ' ');
        $_total_taxe_f    = number_format($_total_taxe, 0, ',', ' ');
        $_total_montant_f = number_format($_total_montant, 0, ',', ' ');

        $_template->setValue('nb_controle', $_nbr_visite);
        $_template->setValue('droit_total', $_total_droit_f);
        $_template->setValue('pv_total', $_total_pv_f);
        $_template->setValue('carnet_total', $_total_carnet_f);
        $_template->setValue('carte_total', $_total_carte_f);
        $_template->setValue('taxe_total', $_total_taxe_f);
        $_template->setValue('montant_total', $_total_montant_f);

        $_template->saveAs($_dest_tmp);

        // Recuperer manager
        $_const_av_ded_manager = $this->_container->get(ServiceName::SRV_METIER_CONST_AV_DED);

        // Convertir en PDF
        $_dest_tmp = $_const_av_ded_manager->convertToPdf($_path, $_file_without_ext);

        if ($_is_omavet) {
            $_template_omavet->setValue('droit_total_omavet', $_total_droit_f);
            $_d_dgsr   = ((int)$_total_droit * 0.8);
            $_d_omavet = ((int)$_total_droit * 0.2);

            $_d_dgsr_f   = number_format($_d_dgsr, 0, ',', ' ');
            $_d_omavet_f = number_format($_d_omavet, 0, ',', ' ');

            $_template_omavet->setValue('d_dgsr', $_d_dgsr_f);
            $_template_omavet->setValue('d_omavet', $_d_omavet_f);

            $_template_omavet->saveAs($_dest_tmp_omavet);

            // Convertir en PDF
            $_dest_tmp_omavet = $_const_av_ded_manager->convertToPdf($_path, $_file_without_ext_omavet);

            return array(
                'download_path_omavet' => $_dest_tmp_omavet,
                'url_path_omavet'      => $_path_omavet_pdf,
                'download_path'        => $_dest_tmp,
                'url_path'             => $_path_pdf
            );
        }

        return array(
            'download_path' => $_dest_tmp,
            'url_path'      => $_path_pdf
        );
    }

    /**
     * Générer un feuille de caisse
     * @param string $_date
     * @return string
     */
    public function generateFeuilleCaisseOld($_date, $_centre_id, $_visite_type_id) {
        // Récupérer manager
        $_usage_tarif_manager        = $this->_container->get(ServiceName::SRV_METIER_USAGE_TARIF);
        $_visite_extra_tarif_manager = $this->_container->get(ServiceName::SRV_METIER_VISITE_EXTRA_TARIF);
        $_pv_manager                 = $this->_container->get(ServiceName::SRV_METIER_PROCES_VERBAL);
        $_centre_manager             = $this->_container->get(ServiceName::SRV_METIER_CENTRE);

        if ($_centre_id > 0) {
            $_centre = $_centre_manager->getCtCentreById($_centre_id);
        } else {
            // Récupérer le centre de l'utilisateur connecté
            $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();
            $_centre         = $_user_connected->getCtCentre();
        }
        $_centre_id = $_centre->getId();

        // Récupération informations
        // Centre et province
        $_nom_centre    = $_centre->getCtrNom();
        $_code_centre   = $_centre->getCtrCode();
        $_nom_province  = $_centre->getCtProvince()->getPrvNom();
        $_code_province = $_centre->getCtProvince()->getPrvCode();

        $_is_omavet = false;

        if (stripos($_nom_centre, "nanisana") !== false) {
            $_is_omavet = true;
        }

        // Récupérer répertoire modèle Word
        $_pv_directory = $this->_container->getParameter('reporting_template_directory');
        $_template_src = $_pv_directory . PathReportingName::TEMPLATE_VISITE_FEUILLE_CAISSE;
        $_date_parsed  = str_replace('-', '_', $_date);
        $_path         = $_pv_directory . PathReportingName::GENERATE_VISITE_FEUILLE_CAISSE;
        if ($_is_omavet) {
            $_template_omavet_src     = $_pv_directory . PathReportingName::TEMPLATE_VISITE_FEUILLE_CAISSE_OMAVET;
            $_num_pv_omavet           = 'feuille_de_caisse_omavet_' . $_date_parsed;
            $_filename_omavet         = strtoupper($_num_pv_omavet);
            $_dest_tmp_omavet         = $_path . $_filename_omavet . '.docx';
            $_file_without_ext_omavet = $_filename_omavet;

            $_url_scheme       = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
            $_path_omavet_docx = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_FEUILLE_CAISSE . $_filename_omavet . '.docx';
            $_path_omavet_pdf  = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_FEUILLE_CAISSE . $_filename_omavet . '.pdf';
        }
        
        $_num_pv   = 'feuille_de_caisse_' . $_code_centre . '_' . $_code_province . '_' . $_date_parsed;
        $_filename = strtoupper($_num_pv);
        
        $_dest_tmp         = $_path . $_filename . '.docx';
        $_file_without_ext = $_filename;

        $_url_scheme = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path_docx  = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_FEUILLE_CAISSE . $_filename . '.docx';
        $_path_pdf   = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_FEUILLE_CAISSE . $_filename . '.pdf';

        $_php_word = new PhpWord();
        $_template = $_php_word->loadTemplate($_template_src);
        
        // Centre et province
        $_template->setValue('province', $_nom_province);
        $_template->setValue('centre', $_nom_centre);

        if ($_is_omavet) {
            $_template_omavet = $_php_word->loadTemplate($_template_omavet_src);
            // Centre et province
            $_template_omavet->setValue('province', $_nom_province);
            $_template_omavet->setValue('centre', $_nom_centre);
        }

        // Visites
        $_visites       = $this->getAllCtVisiteByCentreAndDate($_centre_id, $_date, $_visite_type_id);
        $_nbr_visite    = count($_visites);
        $_total_droit   = 0;
        $_total_pv      = 0;
        $_total_carnet  = 0;
        $_total_carte   = 0;
        $_total_taxe    = 0;
        $_total_montant = 0;
        $_template->cloneRow('date', $_nbr_visite);
        if ($_is_omavet) {
            $_dt = new \DateTime($_date);
            $_date = $_dt->format('d/m/Y');
            $_template_omavet->setValue('date', $_date);
            $_template_omavet->cloneRow('date_omavet', $_nbr_visite);
        }
        foreach ($_visites as $_key => $_visite) {
            ++$_key;

            $_carte_grise  = $_visite->getCtCarteGrise();
            $_usage        = $_visite->getCtUsage();
            $_usage_tarif  = $_usage_tarif_manager->getRepository()->findByCtUsage($_usage->getId());
            $_verificateur = $_visite->getCtVerificateur();
            $_extras       = $_visite->getCtVisiteExtra();
            $_type_visite  = $_visite->getCtTypeVisite();

            $_date_created           = $_visite->getVstCreated();
            $_numero_pv              = $_visite->getVstNumPv();
            $_numero_immatriculation = $_carte_grise->getCgImmatriculation();
            $_usage_effectif         = htmlspecialchars($_usage->getUsgLibelle());
            $_nom_verificateur       = $_verificateur->getUsrName();
            $_nom_cooperative        = $_visite->getCtCarteGrise()->getCgNomCooperative();

            $_utilisation      = $_visite->getCtUtilisation();
            $_util_libelle     = $_utilisation->getUtLibelle();
            $_is_administratif = (stripos($_util_libelle, "administratif") !== false) ? true: false;

            $_droit = 0;
            if ($_usage_tarif)
                $_droit = $_usage_tarif[0]->getUsgTrfPrix();
            $_taxe = 0;
            $_vtb  = 0;

            // PV
            // Si à domicile
            if (stripos($_type_visite->getTpvLibelle(), 'domicile') !== false) {
                $_proces_verbal = $_pv_manager->getRepository()->findByPvType('VT_DOM');
            } elseif (stripos($_type_visite->getTpvLibelle(), 'site') !== false) {
                // Si inapte
                $_proces_verbal = $_pv_manager->getRepository()->findByPvType('VT_INAPTE');

                // Si apte
                if ($_visite->getVstIsApte() == 1)
                    $_proces_verbal = $_pv_manager->getRepository()->findByPvType('VT_APTE');
            }
            $_pv = $_proces_verbal[0]->getPvTarif();

            // Carnet et carte
            $_carnet = 0;
            $_carte  = 0;
            if (count($_extras) > 0) {
                foreach ($_extras as $_extra){
                    $_id_extra = $_extra->getId();

                    // Si carnet
                    if (stripos($_extra->getVsteLibelle(), 'carnet') !== false) {
                        $_visite_extra_tarif = $_visite_extra_tarif_manager->getRepository()->findByCtVisiteExtra(1);
                        if ($_visite_extra_tarif)
                            $_carnet = $_visite_extra_tarif[0]->getVetPrix();
                    }

                    // Si carte
                    if (stripos($_extra->getVsteLibelle(), 'carte') !== false) {
                        $_visite_extra_tarif = $_visite_extra_tarif_manager->getRepository()->findByCtVisiteExtra(2);
                        if ($_visite_extra_tarif)
                            $_carte = $_visite_extra_tarif[0]->getVetPrix();
                    }
                }
            }

            // Taxe
            $_taxe = ($_droit + $_pv + $_carnet + $_carte) * $this->_container->getParameter('taxe');

            // Aptitude
            $_is_apte  = $_visite->getVstIsApte();
            $_aptitude = 'INAPTE';
            if ($_is_apte == 1)
                $_aptitude = 'APTE';

            if ($_is_administratif) {
                $_droit  = 0;
                $_pv     = 0;
                $_carnet = 0;
                $_carte  = 0;
                $_taxe   = 0;
                $_vtb    = 0;
            }

            // Total montant horizontal
            $_total_montant_horizontal = $_droit + $_pv + $_carnet + $_carte + $_taxe + $_vtb;

            // Total montant vertical
            $_total_droit   += $_droit;
            $_total_pv      += $_pv;
            $_total_carnet  += $_carnet;
            $_total_carte   += $_carte;
            $_total_taxe    += $_taxe;
            $_total_montant += $_total_montant_horizontal;

            $_droit_f                    = number_format($_droit, 0, ',', ' ');
            $_pv_f                       = number_format($_pv, 0, ',', ' ');
            $_carnet_f                   = number_format($_carnet, 0, ',', ' ');
            $_carte_f                    = number_format($_carte, 0, ',', ' ');
            $_taxe_f                     = number_format($_taxe, 0, ',', ' ');
            $_total_montant_horizontal_f = number_format($_total_montant_horizontal, 0, ',', ' ');

            $_template->setValue('date#' . $_key, $_date_created->format('d/m/Y'));
            $_template->setValue('ncontrole#' . $_key, $_numero_pv);
            $_template->setValue('imma#' . $_key, $_numero_immatriculation);
            $_template->setValue('usage#' . $_key, $_usage_effectif);
            $_template->setValue('aptitude#' . $_key, $_aptitude);
            $_template->setValue('verif#' . $_key, $_nom_verificateur);
            $_template->setValue('nom_cooperative#' . $_key, htmlspecialchars($_nom_cooperative));
            $_template->setValue('droit#' . $_key, $_droit_f);
            $_template->setValue('pv#' . $_key, $_pv_f);
            $_template->setValue('carnet#' . $_key, $_carnet_f);
            $_template->setValue('carte#' . $_key, $_carte_f);
            $_template->setValue('taxe#' . $_key, $_taxe_f);
            $_template->setValue('vtb#' . $_key, $_vtb);
            $_template->setValue('montant#' . $_key, $_total_montant_horizontal_f);

            if ($_is_omavet) {
                $_template_omavet->setValue('date_omavet#' . $_key, $_date_created->format('d/m/Y'));
                $_template_omavet->setValue('ncontrole_omavet#' . $_key, $_numero_pv);
                $_template_omavet->setValue('imma_omavet#' . $_key, $_numero_immatriculation);
                $_template_omavet->setValue('usage_omavet#' . $_key, $_usage_effectif);
                $_template_omavet->setValue('droit_omavet#' . $_key, $_droit_f);
            }
        }

        $_total_droit_f   = number_format($_total_droit, 0, ',', ' ');
        $_total_pv_f      = number_format($_total_pv, 0, ',', ' ');
        $_total_carnet_f  = number_format($_total_carnet, 0, ',', ' ');
        $_total_carte_f   = number_format($_total_carte, 0, ',', ' ');
        $_total_taxe_f    = number_format($_total_taxe, 0, ',', ' ');
        $_total_montant_f = number_format($_total_montant, 0, ',', ' ');

        $_template->setValue('nb_controle', $_nbr_visite);
        $_template->setValue('droit_total', $_total_droit_f);
        $_template->setValue('pv_total', $_total_pv_f);
        $_template->setValue('carnet_total', $_total_carnet_f);
        $_template->setValue('carte_total', $_total_carte_f);
        $_template->setValue('taxe_total', $_total_taxe_f);
        $_template->setValue('montant_total', $_total_montant_f);

        $_template->saveAs($_dest_tmp);

        // Recuperer manager
        $_const_av_ded_manager = $this->_container->get(ServiceName::SRV_METIER_CONST_AV_DED);

        // Convertir en PDF
        $_dest_tmp = $_const_av_ded_manager->convertToPdf($_path, $_file_without_ext);

        if ($_is_omavet) {
            $_template_omavet->setValue('droit_total_omavet', $_total_droit_f);
            $_d_dgsr   = ((int)$_total_droit * 0.8);
            $_d_omavet = ((int)$_total_droit * 0.2);

            $_d_dgsr_f   = number_format($_d_dgsr, 0, ',', ' ');
            $_d_omavet_f = number_format($_d_omavet, 0, ',', ' ');
            
            $_template_omavet->setValue('d_dgsr', $_d_dgsr_f);
            $_template_omavet->setValue('d_omavet', $_d_omavet_f);

            $_template_omavet->saveAs($_dest_tmp_omavet);

            // Convertir en PDF
            $_dest_tmp_omavet = $_const_av_ded_manager->convertToPdf($_path, $_file_without_ext_omavet);

            return array(
                'download_path_omavet' => $_dest_tmp_omavet,
                'url_path_omavet'      => $_path_omavet_pdf,
                'download_path'        => $_dest_tmp,
                'url_path'             => $_path_pdf
            );
        }

        return array(
            'download_path' => $_dest_tmp,
            'url_path'      => $_path_pdf
        );
    }

    /**
     * Générer un fiche verificateur d'une centre
     * @param integer $_id_centre
     * @param \DateTime $_date
     * @return array
     */
    public function generateFicheVerificateurByCentre($_id_centre,  $_id_verif, $_date = 0) {
        $_verif_manager         = $this->_container->get(ServiceName::SRV_METIER_USER);
        $_centre_manager        = $this->_container->get(ServiceName::SRV_METIER_CENTRE);

        if ($_id_centre > 0) {
            $_centre = $_centre_manager->getCtCentreById($_id_centre);
        } else {
            $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();
            // Traitement centre
            if ($_user_connected->getCtRole()->getId() != RoleName::ID_ROLE_SUPERADMIN) {
                $_centre = $_user_connected->getCtCentre();
            }
        }
        // Récupération informations
        // Centre et province
        $_nom_centre    = $_centre->getCtrNom();
        $_code_centre   = $_centre->getCtrCode();
        $_nom_province  = $_centre->getCtProvince()->getPrvNom();
        $_code_province = $_centre->getCtProvince()->getPrvCode();

        // Récupérer répertoire modèle Word
        $_pv_directory          = $this->_container->getParameter('reporting_template_directory');
        $_template              = $_pv_directory . PathReportingName::TEMPLATE_VISITE_FICHE_VERIFICATEUR;
        $_date_parsed           = str_replace('-', '_', date('d-m-Y'));
        $_num_pv                = 'fiche_verificateur_' . $_code_centre . '_' . $_code_province . '_' . $_date_parsed;
        $_filename              = strtoupper($_num_pv);

        $_path                  = $_pv_directory . PathReportingName::GENERATE_VISITE_FICHE_VERIFICATEUR;
        $_dest_tmp              = $_path . $_filename . '.docx';
        $_file_without_ext      = $_filename;

        $_url_scheme            = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path_docx             = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_FICHE_VERIFICATEUR . $_filename . '.docx';
        $_path_pdf              = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_FICHE_VERIFICATEUR . $_filename . '.pdf';

        $_php_word = new PhpWord();
        $_template = $_php_word->loadTemplate($_template);

        // Centre et province
        $_template->setValue('province', $_nom_province);
        $_template->setValue('centre', $_nom_centre);

        if ($_id_verif > 0) {
            $_liste_verificateurs = $_verif_manager->getVerificateurById($_id_verif);
        } else {
            $_liste_verificateurs   = $_verif_manager->getListVerificateurByCentre($_id_centre);
        }

        $_verificateur = $_liste_verificateurs[0];

        $_array_of_visites  = $this->getCtVisiteByVerificateur($_verificateur->getId(), $_date);
        $_nb_apte = 0;
        $_nb_inapte = 0;

        if (!count($_array_of_visites)) {
            $_date_visite   = " - ";
            $_imma          = " - ";
            $_verif_nom     = $_verificateur->getUsrName();
            $_heure_pass    = " - ";
            $_aptitude      = " - ";
            $_anomalies     = " - ";

            $_template->setValue('date', $_date_visite);
            $_template->setValue('imma', $_imma);
            $_template->setValue('verificateur', htmlspecialchars($_verif_nom));
            $_template->setValue('heure_pass', $_heure_pass);
            $_template->setValue('aptitude', $_aptitude);
            $_template->setValue('anomalie', $_anomalies);
        } else {
            $_template->cloneRow('date', count($_array_of_visites));

            foreach ($_array_of_visites as $_key_visite => $_visite) {
                $_date_visite   = $_visite->getVstCreated();
                $_date_visite_f = $_date_visite->format('d/m/Y');
                $_imma          = $_visite->getCtCarteGrise()->getCgImmatriculation();
                $_verif_nom     = $_verificateur->getUsrName();
                $_heure_pass    = $_date_visite->format('h') . ":" . $_date_visite->format('i');
                $_is_apte       = $_visite->getVstIsApte();
                $_anomalies     = $_visite->getCtAnomalie();
                $_list_an       = "";
                if (count($_anomalies) > 0) {
                    for ($_i = 0; $_i < count($_anomalies) -1; $_i++) {
                        $_anomalie = $_anomalies[$_i];
                        $_list_an .=  $_anomalie->getAnmlCode() . ',';
                    }
                    $_anomalie = $_anomalies[$_i];
                    $_list_an .= $_anomalie->getAnmlCode();
                }
                if ($_is_apte == 1) {
                    $_nb_apte++;
                    $_aptitude  = "APTE";
                } elseif ($_is_apte == 0) {
                    $_nb_inapte++;
                    $_aptitude  = "INAPTE";
                }
                $_template->setValue('date#'.($_key_visite+1), $_date_visite_f);
                $_template->setValue('imma#'.($_key_visite+1), $_imma);
                $_template->setValue('verificateur#'.($_key_visite+1), $_verif_nom);
                $_template->setValue('heure_pass#'.($_key_visite+1), $_heure_pass);
                $_template->setValue('aptitude#'.($_key_visite+1), $_aptitude);
                $_template->setValue('anomalie#'.($_key_visite+1), $_list_an);

            }
        }

        $_template->setValue('apte', $_nb_apte);
        $_template->setValue('inapte', $_nb_inapte);

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
     * Générer Proces Verbal de visite technique
     * @param integer $_visite_id
     * @return array
     */
    public function generatePVVisite($_visite_id)
    {
        $_usage_tarif_manager        = $this->_container->get(ServiceName::SRV_METIER_USAGE_TARIF);
        $_visite_extra_tarif_manager = $this->_container->get(ServiceName::SRV_METIER_VISITE_EXTRA_TARIF);
        $_pv_manager                 = $this->_container->get(ServiceName::SRV_METIER_PROCES_VERBAL);

        $_visite      = $this->getCtVisiteById($_visite_id);
        $_is_apte     = $_visite->getVstIsApte();
        $_num_pv      = $_visite->getVstNumPv();
        $_is_contre_v = $_visite->getVstIsContreVisite();

        $_centre        = $_visite->getCtCentre();
        $_operator      = $_visite->getCtUser()->getUsrName();


        // Récupération informations
        // Centre et province
        $_nom_centre    = $_centre->getCtrNom();
        $_nom_province  = $_centre->getCtProvince()->getPrvNom();

        // Récupérer répertoire modèle Word
        $_pv_directory      = $this->_container->getParameter('reporting_template_directory');
        $_template          = $_pv_directory . PathReportingName::TEMPLATE_VISITE_APTE;
        $_path_reporting    = PathReportingName::GENERATE_VISITE_APTE;
        if ($_is_apte == 0) {
            $_template         = $_pv_directory . PathReportingName::TEMPLATE_VISITE_INAPTE;
        }
        if ($_is_contre_v == 1 && $_is_apte == 1) {
            $_template         = $_pv_directory . PathReportingName::TEMPLATE_CONTRE_VISITE;
        }
        $_path                  = $_pv_directory . $_path_reporting;
        $_filename              = str_replace('/', '_', $_num_pv);
        $_filename              = str_replace(' ', '_', $_filename);
        $_filename              = strtoupper($_filename);

        $_dest_tmp              = $_path . $_filename . '.docx';
        $_file_without_ext      = $_filename;

        $_url_scheme            = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path_docx             = 'http://' . $_url_scheme . '/reporting/' . $_path_reporting . $_filename . '.docx';
        $_path_pdf              = 'http://' . $_url_scheme . '/reporting/' . $_path_reporting . $_filename . '.pdf';

        $_php_word = new PhpWord();
        $_template = $_php_word->loadTemplate($_template);

        // Récupération informations
        $_recu                   = strtoupper($_num_pv);
        $_usage                  = $_visite->getCtUsage();
        $_usage_eff              = htmlspecialchars($_usage->getUsgLibelle());
        $_numero_immatriculation = $_visite->getCtCarteGrise()->getCgImmatriculation();
        $_nom                    = $_visite->getCtCarteGrise()->getCgNom();
        $_prenom                 = $_visite->getCtCarteGrise()->getCgPrenom();
        $_nom_prenom             = $_nom . ' ' . $_prenom;
        $_nom_prenom             = strtoupper($_nom_prenom);
        $_adresse                = $_visite->getCtCarteGrise()->getCgAdresse();
        $_profession             = $_visite->getCtCarteGrise()->getCgProfession();
        $_commune                = $_visite->getCtCarteGrise()->getCgCommune();
        $_marque                 = $_visite->getCtCarteGrise()->getCtVehicule()->getCtMarque()->getMrqLibelle();
        $_type                   = $_visite->getCtCarteGrise()->getCtVehicule()->getVhcType();
        $_genre                  = $_visite->getCtCarteGrise()->getCtVehicule()->getCtGenre()->getGrLibelle();
        $_numero_serie           = $_visite->getCtCarteGrise()->getCtVehicule()->getVhcNumSerie();
        $_numero_moteur          = $_visite->getCtCarteGrise()->getCtVehicule()->getVhcNumMoteur();
        $_carrosserie            = $_visite->getCtCarteGrise()->getCtCarosserie()->getCrsLibelle();
        $_source_energie         = $_visite->getCtCarteGrise()->getCtSourceEnergie()->getSreLibelle();
        $_puissance              = $_visite->getCtCarteGrise()->getCgPuissanceAdmin();
        $_place_assise           = $_visite->getCtCarteGrise()->getCgNbrAssis();
        $_place_debout           = $_visite->getCtCarteGrise()->getCgNbrDebout();
        $_annee_mise_circulation = $_visite->getCtCarteGrise()->getCgMiseEnService() ? $_visite->getCtCarteGrise()->getCgMiseEnService()->format('Y') : '-';
        $_poids_vide             = $_visite->getCtCarteGrise()->getCtVehicule()->getVhcPoidsVide();
        $_charge_utile           = $_visite->getCtCarteGrise()->getCtVehicule()->getVhcChargeUtile();
        $_poids_total_charge     = $_visite->getCtCarteGrise()->getCtVehicule()->getVhcPoidsTotalCharge();
        $_date_visite            = $_visite->getVstCreated()->format('d/m/Y');
        $_duree_reparation       = $_visite->getVstDureeReparation();
        $_date_expiration        = $_visite->getVstDateExpiration()->format('d/m/Y');
        $_num_violet             = $_visite->getCtCarteGrise()->getCgNumCarteViolette();
        $_violet_date            = $_visite->getCtCarteGrise()->getCgDateCarteViolette() ? $_visite->getCtCarteGrise()->getCgDateCarteViolette()->format('d/m/Y') : '-';
        $_num_vignette           = $_visite->getCtCarteGrise()->getCgNumVignette();
        $_vignette_date          = $_visite->getCtCarteGrise()->getCgDateVignette()->format('d/m/Y');
        $_patente                = $_visite->getCtCarteGrise()->getCgPatente();
        $_ani                    = $_visite->getCtCarteGrise()->getCgAni();
        $_rta                    = $_visite->getCtCarteGrise()->getCgRta();
        $_verificateur           = $_visite->getCtVerificateur()->getUsrName();

        $_utilisation            = $_visite->getCtUtilisation();
        $_util_libelle           = $_utilisation->getUtLibelle();
        $_is_administratif       = (stripos($_util_libelle, "administratif") !== false) ? true: false;

//        $_usage_tarif            = $_usage_tarif_manager->getRepository()->findByCtUsage($_usage->getId());

        $_extras                 = $_visite->getCtVisiteExtra();
        $_type_visite            = $_visite->getCtTypeVisite();
        $_usage_tarif            = $_usage_tarif_manager->getCtUsageTarifByUsageAndTypeVisite($_usage->getId(), $_type_visite->getId());

        $_droit = 0;
        if ($_usage_tarif)
            $_droit = $_usage_tarif['usgTrfPrix'];
        $_taxe = 0;
        $_vtb  = 0;

        // PV
        // Si à domicile
        if (stripos($_type_visite->getTpvLibelle(), 'domicile') !== false) {
            $_proces_verbal = $_pv_manager->getRepository()->findByPvType('VT_DOM');
        } elseif (stripos($_type_visite->getTpvLibelle(), 'site') !== false) {
            // Si apte
            if ($_visite->getVstIsApte() == 1) {
                $_proces_verbal = $_pv_manager->getRepository()->findByPvType('VT_APTE');
            } else { // Si inapte
                $_proces_verbal = $_pv_manager->getRepository()->findByPvType('VT_INAPTE');
            }
        }
        $_pv = $_proces_verbal[0]->getPvTarif();

        // Carnet et carte
        $_carnet = 0;
        $_carte  = 0;
        if (count($_extras) > 0) {
            foreach ($_extras as $_extra){
                // Si carnet
                if (stripos($_extra->getVsteLibelle(), 'carnet') !== false) {
                    $_visite_extra_tarif = $_visite_extra_tarif_manager->getRepository()->findByCtVisiteExtra(1);
                    if ($_visite_extra_tarif)
                        $_carnet = $_visite_extra_tarif[0]->getVetPrix();
                }

                // Si carte
                if (stripos($_extra->getVsteLibelle(), 'carte') !== false) {
                    $_visite_extra_tarif = $_visite_extra_tarif_manager->getRepository()->findByCtVisiteExtra(2);
                    if ($_visite_extra_tarif)
                        $_carte = $_visite_extra_tarif[0]->getVetPrix();
                }
            }
        }

        $_tht  = $_droit + $_pv + $_carnet + $_carte;

        // Taxe
        $_taxe = $_tht * $this->_container->getParameter('taxe');

        // Total montant
        $_total_montant = $_tht + $_taxe + $_vtb;

        $_template->setValue('province', htmlspecialchars($_nom_province));
        $_template->setValue('centre', htmlspecialchars($_nom_centre));
        $_template->setValue('Recu', htmlspecialchars($_recu));
        $_template->setValue('date', htmlspecialchars($_date_visite));
        $_template->setValue('nom', htmlspecialchars($_nom_prenom));
        $_template->setValue('immatr', htmlspecialchars($_numero_immatriculation));

        $_template->setValue('verificateur', htmlspecialchars($_verificateur));
        $_template->setValue('operateur', htmlspecialchars($_operator));
        $_template->setValue('expiration', htmlspecialchars($_date_expiration));
        $_template->setValue('adresse', htmlspecialchars($_adresse));
        $_template->setValue('profession', htmlspecialchars($_profession));
        $_template->setValue('marque', htmlspecialchars($_marque));
        $_template->setValue('commune', htmlspecialchars($_commune));
        $_template->setValue('genre', htmlspecialchars($_genre));
        $_template->setValue('type', htmlspecialchars($_type));
        $_template->setValue('carrosserie', htmlspecialchars($_carrosserie));
        $_template->setValue('source', htmlspecialchars($_source_energie));
        $_template->setValue('puissance', htmlspecialchars($_puissance));
        $_template->setValue('num_serie_type', htmlspecialchars($_numero_serie));
        $_template->setValue('num_moteur', htmlspecialchars($_numero_moteur));
        $_template->setValue('assis', htmlspecialchars($_place_assise));
        $_template->setValue('debout', htmlspecialchars($_place_debout));
        $_template->setValue('ptc', htmlspecialchars($_poids_total_charge));
        $_template->setValue('p_vide', htmlspecialchars($_poids_vide));
        $_template->setValue('cu', htmlspecialchars($_charge_utile));
        $_template->setValue('mise_annee_service', htmlspecialchars($_annee_mise_circulation));
        $_template->setValue('usage_eff', htmlspecialchars($_usage_eff));
        $_template->setValue('num_violet', htmlspecialchars($_num_violet));
        $_template->setValue('violet_date', htmlspecialchars($_violet_date));
        $_template->setValue('num_vignette', htmlspecialchars($_num_vignette));
        $_template->setValue('vignette_date', htmlspecialchars($_vignette_date));
        $_template->setValue('pattente', htmlspecialchars($_patente));
        $_template->setValue('ani', htmlspecialchars($_ani));
        $_template->setValue('rta', htmlspecialchars($_rta));
        $_template->setValue('reparation', htmlspecialchars($_duree_reparation));

        if ($_is_administratif) {
            $_template->setValue('total', 0);
            $_template->setValue('usage', 0);
            $_template->setValue('carnet', 0);
            $_template->setValue('pv', 0);
            $_template->setValue('carte', 0);
            $_template->setValue('hors', 0);
            $_template->setValue('TVA', 0);
        } else {
            $_total_montant_f     = number_format($_total_montant, 0, ',', ' ');
            $_droit_f     = number_format($_droit, 0, ',', ' ');
            $_carnet_f     = number_format($_carnet, 0, ',', ' ');
            $_pv_f     = number_format($_pv, 0, ',', ' ');
            $_carte_f     = number_format($_carte, 0, ',', ' ');
            $_tht_f     = number_format($_tht, 0, ',', ' ');
            $_taxe_f     = number_format($_taxe, 0, ',', ' ');

            $_template->setValue('total', $_total_montant_f);
            $_template->setValue('usage', $_droit_f);
            $_template->setValue('carnet', $_carnet_f);
            $_template->setValue('pv', $_pv_f);
            $_template->setValue('carte', $_carte_f);
            $_template->setValue('hors', $_tht_f);
            $_template->setValue('TVA', $_taxe_f);
        }

        if ($_is_apte == 0) {
            $_aptitude  = "INAPTE";
            $_anomalies     = $_visite->getCtAnomalie();
            $_template->cloneRow('an', count($_anomalies));

            foreach ($_anomalies as $_key => $_anomalie) {
                $_template->setValue('an#'.($_key + 1), $_anomalie->getAnmlLibelle());
            }
            $_template->setValue('aptitude', $_aptitude);

        } else {
            $_aptitude  = "APTE";
            $_template->setValue('aptitude', $_aptitude);
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
     * Générer un fiche anomalie d'une centre
     * @param integer $_id_centre
     * @param \DateTime $_date
     * @return array
     */
    public function generateFicheAnomalieByCentre($_id_centre,  $_date = 0) {
        $_verif_manager         = $this->_container->get(ServiceName::SRV_METIER_USER);
        $_centre_manager        = $this->_container->get(ServiceName::SRV_METIER_CENTRE);

        if ($_id_centre > 0) {
            $_centre = $_centre_manager->getCtCentreById($_id_centre);
        } else {
            $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();
            // Traitement centre
            if ($_user_connected->getCtRole()->getId() != RoleName::ID_ROLE_SUPERADMIN) {
                $_centre = $_user_connected->getCtCentre();
            }
        }


        // Récupération informations
        // Centre et province
        $_nom_centre    = $_centre->getCtrNom();
        $_code_centre   = $_centre->getCtrCode();
        $_nom_province  = $_centre->getCtProvince()->getPrvNom();
        $_code_province = $_centre->getCtProvince()->getPrvCode();

        // Récupérer répertoire modèle Word
        $_pv_directory          = $this->_container->getParameter('reporting_template_directory');
        $_template              = $_pv_directory . PathReportingName::TEMPLATE_VISITE_FICHE_ANOMALIE;
        $_date_parsed           = str_replace('-', '_', date('d-m-Y'));
        $_num_pv                = 'liste_anomalies_' . $_code_centre . '_' . $_code_province . '_' . $_date_parsed;
        $_filename              = strtoupper($_num_pv);

        $_path                  = $_pv_directory . PathReportingName::GENERATE_VISITE_FICHE_ANOMALIE;
        $_dest_tmp              = $_path . $_filename . '.docx';
        $_file_without_ext      = $_filename;

        $_url_scheme            = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path_docx             = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_FICHE_ANOMALIE . $_filename . '.docx';
        $_path_pdf              = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_FICHE_ANOMALIE . $_filename . '.pdf';

        $_php_word = new PhpWord();
        $_template = $_php_word->loadTemplate($_template);

        // Centre et province
        $_template->setValue('province', $_nom_province);
        $_template->setValue('centre', $_nom_centre);

        // Formattage date
        $_date_exploded = explode('-', $_date);
        $_date_parsed   = $_date_exploded[2] . '/' . $_date_exploded[1] . '/' . $_date_exploded[0];

        $_template->setValue('date', $_date_parsed);

        $_id_centre = $_centre->getId();
        $_array_of_visites  = $this->getAllCtVisiteByCentreAndDate($_id_centre, $_date, 0, "INAPTE");

        if (!count($_array_of_visites)) {
            $_imma          = " - ";
            $_verif_nom     = " - ";
            $_aptitude      = " - ";
            $_ncontrole     = " - ";
            $_total         = " - ";
            $_template->setValue('imma', $_imma);
            $_template->setValue('verif', $_verif_nom);
            $_template->setValue('ncontrole', $_ncontrole);
            $_template->setValue('aptitude', $_aptitude);
            $_template->setValue('total', $_total);
        } else {
            $_template->cloneRow('ncontrole', count($_array_of_visites));

            foreach ($_array_of_visites as $_key_visite => $_visite) {
                $_date_visite   = $_visite->getVstCreated();
                $_imma          = $_visite->getCtCarteGrise()->getCgImmatriculation();
                $_verif_nom     = $_visite->getCtVerificateur()->getUsrName();
                $_ncontrole     = $_visite->getVstNumPv();
                $_anomalies     = $_visite->getCtAnomalie();
                $_list_an       = "";
                if (count($_anomalies) > 0) {
                    for ($_i = 0; $_i < count($_anomalies) -1; $_i++) {
                        $_anomalie = $_anomalies[$_i];
                        $_list_an .=  $_anomalie->getAnmlCode() . ',';
                    }
                    $_anomalie = $_anomalies[$_i];
                    $_list_an .= $_anomalie->getAnmlCode();
                }


                $_template->setValue('ncontrole#'.($_key_visite+1), $_ncontrole);
                $_template->setValue('imma#'.($_key_visite+1), $_imma);
                $_template->setValue('verif#'.($_key_visite+1), $_verif_nom);
                $_template->setValue('aptitude#'.($_key_visite+1), $_list_an);

            }
            $_template->setValue('total', count($_array_of_visites));
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
     * Vérification si le dernier précédent du visite est inapte
     * @param \DateTime $_visite_date
     * @param string $_immatriculation
     * @return array
     */
    public function isInapteLastVisite($_visite_date, $_immatriculation)
    {
        $_visite = EntityName::CT_VISITE;

        $_dql = "SELECT vst FROM $_visite vst
                 LEFT JOIN vst.ctCarteGrise cg
                 WHERE vst.vstCreated < :date_visite
                 AND cg.cgImmatriculation = :immatriculation
                 ORDER BY vst.vstCreated DESC";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('date_visite', $_visite_date);
        $_query->setParameter('immatriculation', $_immatriculation);
        $_query->setMaxResults(1);

        $_result = $_query->getResult();
        if (count($_result) > 0) {
            if ($_result[0]->getVstIsApte() == false)
                return true;
        }

        return false;
    }
}
