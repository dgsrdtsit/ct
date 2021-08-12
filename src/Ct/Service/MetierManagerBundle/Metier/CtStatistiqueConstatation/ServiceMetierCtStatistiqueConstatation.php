<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtStatistiqueConstatation;

use Ct\Service\MetierManagerBundle\Utils\BilanReception;
use Ct\Service\MetierManagerBundle\Utils\PathReportingName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtStatistiqueConstatation
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
     * Récupérer la liste de constatation
     * @param integer $_id_centre
     * @param integer $_annee
     * @param integer $_value
     * @param string $_interval
     * @param string $_rec_type
     * @return array
     */
    public function getListConstatation($_id_centre, $_annee, $_value = 0, $_interval = "annuel")
    {
        $_e_constatation       = EntityName::CT_CONST_AV_DED;
        $_e_genre           = EntityName::CT_GENRE;
        $_e_genre_categorie = EntityName::CT_GENRE_CATEGORIE;
        $_e_droit_ptac      = EntityName::CT_DROIT_PTAC;


        if ($_interval == "mensuel") {
            $_and_stmt = " AND MONTH(cad.cadCreated) = :mois ";
        } elseif ($_interval == "trimestriel") {
            if ($_value == 1)
                $_where_month = '1, 2, 3';
            if ($_value == 2)
                $_where_month = '4, 5, 6';
            if ($_value == 3)
                $_where_month = '7, 8, 9';
            if ($_value == 4)
                $_where_month = '10, 11, 12';
            $_and_stmt = " AND MONTH(cad.cadCreated) IN ($_where_month) ";
        } elseif ($_interval == "semestriel") {
            if ($_value == 1)
                $_where_month = '1, 2, 3, 4, 5, 6';
            if ($_value == 2)
                $_where_month = '7, 8, 9, 10, 11, 12';
            $_and_stmt = " AND MONTH(cad.cadCreated) IN ($_where_month) ";
        } else {
            $_and_stmt = " ";
        }

        $_dql = "SELECT gcat.gcLibelle as libelle, cadcar.cadPoidsTotalCharge as ptac 
                FROM $_e_constatation cad
                LEFT JOIN cad.ctConstAvDedCaracs cadcar
                LEFT JOIN cadcar.ctConstAvDedType cadtype
                LEFT JOIN cadcar.ctGenre ge
                LEFT JOIN ge.ctGenreCategorie gcat
                LEFT JOIN cad.ctCentre ctr
                WHERE (gcat.gcLibelle LIKE '%vehicule a moteur%' OR gcat.gcLibelle LIKE '%remorque et semi-remorque%')
                AND ctr.id = :id_centre
                AND cadtype.cadTpLibelle LIKE '%corps du vehicule%'
                AND YEAR(cad.cadCreated) = :annee
                $_and_stmt
                GROUP BY cad.id";
        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('annee', $_annee);
        $_query->setParameter('id_centre', $_id_centre);
        if ($_interval == "mensuel") {
            $_query->setParameter('mois', intval($_value));
        }

        return $_query->getResult();
    }

    /**
     * Récupérer la liste constatation par genre
     * @param integer $_id_centre
     * @param integer $_annee
     * @param integer $_value
     * @param string $_interval
     * @return array
     */
    public function getTotalConstatationByGenre($_id_centre, $_annee, $_value = 0, $_interval = "annuel")
    {
        $_list_constatations = $this->getListConstatation($_id_centre, $_annee, $_value, $_interval);
        $_vam_3 = 0;
        $_vam_7 = 0;
        $_vam_10 = 0;
        $_vam_19 = 0;
        $_resr_3 = 0;
        $_resr_7 = 0;
        $_resr_10 = 0;
        $_resr_19 = 0;
        foreach ($_list_constatations as $_list) {
            $_genre_libelle = $_list["libelle"];
            $_ptac          = $_list['ptac'];
            $_ptac_tonne    = $_ptac/1000;

            if (stripos($_genre_libelle, "VÉHICULE À MOTEUR") !== false) {

                if ((3.5 <= $_ptac_tonne) && ($_ptac_tonne < 7)) {
                    $_vam_3++;
                }
                if ((7 <= $_ptac_tonne) && ($_ptac_tonne < 10)) {
                    $_vam_7++;
                }
                if ((10 <= $_ptac_tonne) && ($_ptac_tonne < 19)) {
                    $_vam_10++;
                }
                if ((19 <= $_ptac_tonne) && ($_ptac_tonne < 26)) {
                    $_vam_19++;
                }

            }
            if (stripos($_genre_libelle, "REMORQUE ET SEMI-REMORQUE") !== false) {
                if ((3.5 <= $_ptac_tonne) && ($_ptac_tonne < 7)) {
                    $_resr_3++;
                }
                if ((7 <= $_ptac_tonne) && ($_ptac_tonne < 10)) {
                    $_resr_7++;
                }
                if ((10 <= $_ptac_tonne) && ($_ptac_tonne < 19)) {
                    $_resr_10++;
                }
                if ((19 <= $_ptac_tonne) && ($_ptac_tonne < 26)) {
                    $_resr_19++;
                }
            }
        }
        $_vam_tot = $_vam_3 + $_vam_7 + $_vam_10 + $_vam_19;
        $_resr_tot = $_resr_3 + $_resr_7 + $_resr_10 + $_resr_19;
        $_matches = array(
            'vam_3' => $_vam_3,
            'vam_7' => $_vam_7,
            'vam_10' => $_vam_10,
            'vam_19' => $_vam_19,
            'vam_tot' => $_vam_tot,
            'resr_3' => $_resr_3,
            'resr_7' => $_resr_7,
            'resr_10' => $_resr_10,
            'resr_19' => $_resr_19,
            'resr_tot' => $_resr_tot,
        );
        return $_matches;
    }

    /**
     * Générer un reporting
     * @param integer $_id_centre
     * @param integer $_annee
     * @param integer $_value
     * @param string $_interval
     * @param string $_type_recep
     * @return string
     */
    public function generateReportingByInterval($_id_centre, $_annee, $_value = 0, $_interval = "annuel")
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

        if ($_interval == "mensuel") {
            $_pref_num_pv = 'bilan_mensuel_'.$_value.'_';
        } elseif ($_interval == "trimestriel") {
            $_pref_num_pv = 'bilan_trimestriel_'.$_value.'_';
        } elseif ($_interval == "semestriel") {
            $_pref_num_pv = 'bilan_semestriel_'.$_value.'_';
        } else {
            $_pref_num_pv = 'bilan_annuel_';
        }

        $_tpl_reporting_name = PathReportingName::TEMPLATE_CONSTATATION_BILAN;
        $_gnr_reporting_name = PathReportingName::GENERATE_CONSTATATION_BILAN;

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
        $_totales = $this->getTotalConstatationByGenre($_id_centre, $_annee, $_value, $_interval);

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

        $_vam_3 = $_totales['vam_3'];
        $_vam_7 = $_totales['vam_7'];
        $_vam_10 = $_totales['vam_10'];
        $_vam_19 = $_totales['vam_19'];
        $_vam_tot = $_totales['vam_tot'];
        $_resr_3 = $_totales['resr_3'];
        $_resr_7 = $_totales['resr_7'];
        $_resr_10 = $_totales['resr_10'];
        $_resr_19 = $_totales['resr_19'];
        $_resr_tot = $_totales['resr_tot'];
        
        $_template->setValue('vam_3', $_vam_3);
        $_template->setValue('vam_7', $_vam_7);
        $_template->setValue('vam_10', $_vam_10);
        $_template->setValue('vam_19', $_vam_19);
        $_template->setValue('vam_tot', $_vam_tot);
        $_template->setValue('resr_3', $_resr_3);
        $_template->setValue('resr_7', $_resr_7);
        $_template->setValue('resr_10', $_resr_10);
        $_template->setValue('resr_19', $_resr_19);
        $_template->setValue('resr_tot', $_resr_tot);
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
