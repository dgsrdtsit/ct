<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtStatistiqueReception;

use Ct\Service\MetierManagerBundle\Entity\CtReception;
use Ct\Service\MetierManagerBundle\Metier\CtCentre\ServiceMetierCtCentre;
use Ct\Service\MetierManagerBundle\Utils\BilanReception;
use Ct\Service\MetierManagerBundle\Utils\PathReportingName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtStatistiqueReception
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
     * Récupérer la liste reception par type (payant, gratuit)
     * @param string $_type_util Type utilisation
     * @param integer $_id_centre
     * @param integer $_annee
     * @param integer $_value
     * @param string $_interval
     * @param string $_rec_type
     * @return array
     */
    public function getListReceptionWithoutCycloGenre($_type_util, $_id_centre, $_annee, $_value = 0, $_interval = "annuel", $_rec_type = "all")
    {
        $_e_reception       = EntityName::CT_RECEPTION;
        $_e_genre           = EntityName::CT_GENRE;

        $_sql_type = '';
        if ($_type_util == 'payant')
            $_sql_type = 'ut.id = 2 ';
        if ($_type_util == 'gratuit')
            $_sql_type = 'ut.id = 1 ';
        if ($_type_util == 'total'){
            $_sql_type .= '(ut.id = 2 OR ';
            $_sql_type .= 'ut.id = 1) ';
        }

        if ($_interval == "mensuel") {
            $_and_stmt = "AND MONTH(r.rcpCreated) = :mois) ";
        } elseif ($_interval == "trimestriel") {
            if ($_value == 1)
                $_where_month = '1, 2, 3';
            if ($_value == 2)
                $_where_month = '4, 5, 6';
            if ($_value == 3)
                $_where_month = '7, 8, 9';
            if ($_value == 4)
                $_where_month = '10, 11, 12';
            $_and_stmt = "AND MONTH(r.rcpCreated) IN ($_where_month) ";
        } else {
            $_and_stmt = "";
        }
        if ($_rec_type == 'all') {
            $_rec_tp_stmt = "";
        } else {
            $_rec_tp_stmt = " AND tr.tprcpLibelle LIKE :type ";
        }

        $_dql = "SELECT ge.id, ge.grCode as code, 
                (SELECT count(r.id) FROM $_e_reception r
                LEFT JOIN r.ctTypeReception tr
                LEFT JOIN r.ctUtilisation ut
                LEFT JOIN r.ctCentre ctr
                LEFT JOIN r.ctVehicule vhc
                LEFT JOIN vhc.ctGenre vge
                LEFT JOIN vge.ctGenreCategorie gc
                WHERE $_sql_type
                $_rec_tp_stmt
                AND gc.gcLibelle NOT LIKE :categorie
                AND vge.id = ge.id
                AND ctr.id = :id_centre
                AND YEAR(r.rcpCreated) = :annee
                $_and_stmt
                GROUP BY ge.id) as total
                FROM $_e_genre ge
                LEFT JOIN ge.ctGenreCategorie gec
                WHERE gec.gcLibelle NOT LIKE :categorie
                ORDER BY ge.id ASC";
        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('categorie', 'CYCLOMOTEUR');
        if (strlen($_rec_tp_stmt)) {
            $_query->setParameter('type', '%' . $_rec_type . '%');
        }
        $_query->setParameter('annee', $_annee);
        $_query->setParameter('id_centre', $_id_centre);
        if ($_interval == "mensuel") {
            $_query->setParameter('mois', $_value);
        }

        return $_query->getResult();
    }

    /**
     * Récupérer la liste reception par type (payant, gratuit)
     * @param string $_type_util Type utilisation
     * @param integer $_id_centre
     * @param integer $_annee
     * @param integer $_value
     * @param string $_interval
     * @param string $_rec_type
     * @return array
     */
    public function getListReception($_type_util, $_id_centre, $_annee, $_value = 0, $_interval = "annuel", $_rec_type = "all")
    {
        $_e_reception       = EntityName::CT_RECEPTION;
        $_e_genre           = EntityName::CT_GENRE;

        $_sql_type = '';
        if ($_type_util == 'payant')
            $_sql_type = 'ut.id = 2 ';
        if ($_type_util == 'gratuit')
            $_sql_type = 'ut.id = 1 ';
        if ($_type_util == 'total'){
            $_sql_type .= '(ut.id = 2 OR ';
            $_sql_type .= 'ut.id = 1) ';
        }

        if ($_interval == "mensuel") {
            $_and_stmt = "AND MONTH(r.rcpCreated) = :mois ";
        } elseif ($_interval == "trimestriel") {
            if ($_value == 1)
                $_where_month = '1, 2, 3';
            if ($_value == 2)
                $_where_month = '4, 5, 6';
            if ($_value == 3)
                $_where_month = '7, 8, 9';
            if ($_value == 4)
                $_where_month = '10, 11, 12';
            $_and_stmt = "AND MONTH(r.rcpCreated) IN ($_where_month) ";
        } elseif ($_interval == "semestriel") {
            if ($_value == 1)
                $_where_month = '1, 2, 3, 4, 5, 6';
            if ($_value == 2)
                $_where_month = '7, 8, 9, 10, 11, 12';
            $_and_stmt = "AND MONTH(r.rcpCreated) IN ($_where_month) ";
        } else {
            $_and_stmt = " ";
        }

        if ($_rec_type == 'all') {
            $_rec_tp_stmt = "";
        } else {
            $_rec_tp_stmt = " AND tr.tprcpLibelle LIKE :type ";
        }

        $_dql = "SELECT ge.id, ge.grCode as code, ge.grLibelle as libelle,  
                (SELECT count(r.id) FROM $_e_reception r
                LEFT JOIN r.ctTypeReception tr
                LEFT JOIN r.ctUtilisation ut
                LEFT JOIN r.ctCentre ctr
                LEFT JOIN r.ctVehicule vhc
                LEFT JOIN vhc.ctGenre vge
                WHERE $_sql_type
                $_rec_tp_stmt
                AND vge.id = ge.id
                AND ctr.id = :id_centre
                AND YEAR(r.rcpCreated) = :annee
                $_and_stmt
                GROUP BY ge.id) as total
                FROM $_e_genre ge
                ORDER BY ge.id ASC";
        $_query = $this->_entity_manager->createQuery($_dql);
        if (strlen($_rec_tp_stmt)) {
            $_query->setParameter('type', '%' . $_rec_type . '%');
        }
        $_query->setParameter('annee', $_annee);
        $_query->setParameter('id_centre', $_id_centre);
        if ($_interval == "mensuel") {
            $_query->setParameter('mois', $_value);
        }

        return $_query->getResult();
    }

    /**
     * Récupérer la liste reception par genre
     * @param string $_type_util Type utilisation
     * @param integer $_id_centre
     * @param integer $_annee
     * @param integer $_value
     * @param string $_interval
     * @param string $_rec_type
     * @return array
     */
    public function getTotalReceptionByGenre($_type_util, $_id_centre, $_annee, $_value = 0, $_interval = "annuel", $_rec_type = "all")
    {
        $_list_receptions = $this->getListReception($_type_util, $_id_centre, $_annee, $_value, $_interval, $_rec_type);
        $_list_genres = array(
            "VP", "TCP", "CAMIONNETTE", "REM",
            "SREM", "CAMION", "TRR", "VTST",
            "VTSU", "VELO", "CYCLO", "MOTO",
            "REA", "SREA", "TRA", "QUAD"
        );
        $_matches = array();
        foreach ($_list_receptions as $_list) {
            $_genre_libelle = $_list["libelle"];
            $_total         = $_list["total"];
            foreach($_list_genres as $k=>$v) {
                if(preg_match("/\b$v\b/i", $_genre_libelle)) {
                    $_matches[$v] = $_total;
                }
            }
        }
        $_matches_keys = array_keys($_matches);
        foreach ($_list_genres as $_key) {
            if (!in_array($_key, $_matches_keys)) {
                $_matches[$_key] = 0;
            }
        }
        return $_matches;
    }

    /**
     * Récupérer la totale reception par type (payant, gratuit)
     * @param string $_type_util Type utilisation
     * @param integer $_id_centre
     * @param integer $_annee
     * @param integer $_value
     * @param string $_interval
     * @param string $_rec_type
     * @return array
     */
    public function getTotalReceptionByType($_type_util, $_id_centre, $_annee, $_value = 0, $_interval = "annuel", $_rec_type = "all")
    {
        $_e_reception       = EntityName::CT_RECEPTION;
        $_e_genre           = EntityName::CT_GENRE;

        $_sql_type = '';
        if ($_type_util == 'payant')
            $_sql_type = 'ut.id = 2 ';
        if ($_type_util == 'gratuit')
            $_sql_type = 'ut.id = 1 ';
        if ($_type_util == 'total'){
            $_sql_type .= 'ut.id > 0 ';
//            $_sql_type .= 'ut.id = 1) ';
        }

        if ($_interval == "mensuel") {
            $_and_stmt = "AND MONTH(r.rcpCreated) = :mois) ";
        } elseif ($_interval == "trimestriel") {
            if ($_value == 1)
                $_where_month = '1, 2, 3';
            if ($_value == 2)
                $_where_month = '4, 5, 6';
            if ($_value == 3)
                $_where_month = '7, 8, 9';
            if ($_value == 4)
                $_where_month = '10, 11, 12';
            $_and_stmt = "AND MONTH(r.rcpCreated) IN ($_where_month) ";
        } elseif ($_interval == "semestriel") {
            if ($_value == 1)
                $_where_month = '1, 2, 3, 4, 5, 6';
            if ($_value == 2)
                $_where_month = '7, 8, 9, 10, 11, 12';
            $_and_stmt = "AND MONTH(r.rcpCreated) IN ($_where_month) ";
        } else {
            $_and_stmt = " ";
        }
        if ($_rec_type == 'all') {
            $_rec_tp_stmt = "";
        } else {
            $_rec_tp_stmt = " AND tr.tprcpLibelle LIKE :type ";
        }

        $_dql    = "SELECT count(r.id) as total FROM $_e_reception r
                    LEFT JOIN r.ctTypeReception tr
                    LEFT JOIN r.ctUtilisation ut
                    LEFT JOIN r.ctCentre ctr
                    LEFT JOIN r.ctVehicule vhc
                    LEFT JOIN vhc.ctGenre vge
                    WHERE $_sql_type
                    $_rec_tp_stmt
                    AND ctr.id = :id_centre
                    AND YEAR(r.rcpCreated) = :annee
                    $_and_stmt";

        $_query = $this->_entity_manager->createQuery($_dql);
        if (strlen($_rec_tp_stmt)) {
            $_query->setParameter('type', '%'.$_rec_type.'%');
        }
        $_query->setParameter('annee', $_annee);
        $_query->setParameter('id_centre', $_id_centre);
        if ($_interval == "mensuel") {
            $_query->setParameter('mois', $_value);
        }

        return $_query->getSingleResult();
    }

    /**
     * Générer un reporting annuel
     * @param integer $_id_centre
     * @param integer $_annee
     * @param string $_type_recep
     * @param string $_type_util
     * @return string
     */
    public function generateReportingByInterval($_id_centre, $_annee, $_value = 0, $_interval = "annuel", $_type_recep)
    {
        // Récupérer manager
        $_centre_manager = $this->_container->get(ServiceName::SRV_METIER_CENTRE);


        // Récupération informations
        // Centre et province
        $_centre = $_centre_manager->getCtCentreById($_id_centre);
        $_nom_centre = $_centre->getCtrNom();
        $_code_centre = $_centre->getCtrCode();
        $_nom_province = $_centre->getCtProvince()->getPrvNom();
        $_code_province = $_centre->getCtProvince()->getPrvCode();

        if (stripos($_type_recep, "type") !== false) {
            if ($_interval == "mensuel") {
                $_pref_num_pv = 'bilan_mensuel_type_'.$_value.'_';
            } elseif ($_interval == "trimestriel") {
                $_pref_num_pv = 'bilan_trimestriel_type_'.$_value.'_';
            } elseif ($_interval == "semestriel") {
                $_pref_num_pv = 'bilan_semestriel_type_'.$_value.'_';
            } else {
                $_pref_num_pv = 'bilan_annuel_type_';
            }
        } elseif (stripos($_type_recep, "isole") !== false) {
            if ($_interval == "mensuel") {
                $_pref_num_pv = 'bilan_mensuel_isole_'.$_value.'_';
            } elseif ($_interval == "trimestriel") {
                $_pref_num_pv = 'bilan_trimestriel_isole_'.$_value.'_';
            } elseif ($_interval == "semestriel") {
                $_pref_num_pv = 'bilan_semestriel_isole_'.$_value.'_';
            } else {
                $_pref_num_pv = 'bilan_annuel_isole_';
            }
        } else { // ALL
            if ($_interval == "mensuel") {
                $_pref_num_pv = 'bilan_mensuel_tous_'.$_value.'_';
            } elseif ($_interval == "trimestriel") {
                $_pref_num_pv = 'bilan_trimestriel_tous_'.$_value.'_';
            } elseif ($_interval == "semestriel") {
                $_pref_num_pv = 'bilan_semestriel_tous_'.$_value.'_';
            } else {
                $_pref_num_pv = 'bilan_annuel_tous_';
            }
        }

        $_tpl_reporting_name = PathReportingName::TEMPLATE_REC_BILAN;
        $_gnr_reporting_name = PathReportingName::GENERATE_REC_BILAN;

        // Récupérer répertoire modèle Word
        $_reporting_directory = $this->_container->getParameter('reporting_template_directory');
        $_template_bilan = $_reporting_directory . $_tpl_reporting_name;
        $_num_pv = $_pref_num_pv . $_nom_centre . '_' . $_code_centre . '_' . $_code_province . '_' . $_annee;
        $_filename = strtoupper($_num_pv);
        $_path = $_reporting_directory . $_gnr_reporting_name;
        $_dest_tmp = $_path . $_filename . '.docx';
        $_file_without_ext = $_filename;

        $_url_scheme = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path_docx = 'http://' . $_url_scheme . '/reporting/' . $_gnr_reporting_name . $_filename . '.docx';
        $_path_pdf = 'http://' . $_url_scheme . '/reporting/' . $_gnr_reporting_name . $_filename . '.pdf';

        $_php_word = new PhpWord();
        $_template = $_php_word->loadTemplate($_template_bilan);

        // Centre et province
        $_template->setValue('province', $_nom_province);
        $_template->setValue('centre', $_nom_centre);

        // Annee
        $_template->setValue('annee', $_annee);

        if ($_interval == "trimestriel" || $_interval == "semestriel") {
            $_template->setValue('num_interval', $_value);
        }

        // Total
        $_totales = $this->getTotalReceptionByGenre('total', $_id_centre, $_annee, $_value, $_interval, $_type_recep);
        if ($_type_recep == 'all') {
            $_template->setValue('type_recep', '');
        } else {
            $_template->setValue('type_recep', strtoupper($_type_recep));
        }

        $_list_mois = array(1 => "JANVIER", 2 => "FÉVRIER", 3 => "MARS",
            4 => "AVRIL", 5 => "MAI", 6 => "JUIN",
            7 => "JUILLET", 8 => "AOÛT", 9 => "SEPTEMBRE",
            10 => "OCTOBRE", 11 => "NOVEMBRE", 12 => "DÉCEMBRE");

        if ($_interval == 'annuel') {
            $_template->setValue('value', '');
            $_template->setValue('rank', '');
            $_template->setValue('interval', 'Année');
        } elseif ($_interval == 'semestriel') {
            $_template->setValue('value', $_value);
            if ($_value == 1) {
                $_template->setValue('rank', 'er');
            } else {
                $_template->setValue('rank', 'ème');
            }
            $_template->setValue('interval', 'SEMESTRE');
        } elseif ($_interval == 'trimestriel') {
            $_template->setValue('value', $_value);
            if ($_value == 1) {
                $_template->setValue('rank', 'er');
            } else {
                $_template->setValue('rank', 'ème');
            }
            $_template->setValue('interval', 'TRIMESTRE');
        } elseif ($_interval == 'mensuel') {
            $_template->setValue('value', 'MOIS DE ' . $_list_mois[$_value]);
            $_template->setValue('rank', '');
            $_template->setValue('interval', '');
        }

        $_sum_total = 0;
        foreach ($_totales as $_key => $_val) {
            $_libelle = strtolower($_key);
            $_total = empty($_val) ? 0 : $_val;
            $_template->setValue($_libelle, $_total);
            $_sum_total += $_total;
        }

        $_template->setValue('total', $_sum_total);
        $_template->saveAs($_dest_tmp);

        // Recuperer manager
        $_const_av_ded_manager = $this->_container->get(ServiceName::SRV_METIER_CONST_AV_DED);
        // Convertir en PDF
        $_dest_tmp = $_const_av_ded_manager->convertToPdf($_path, $_file_without_ext);

        return array(
            'download_path' => $_dest_tmp,
            'url_path' => $_path_pdf
        );
    }
}
