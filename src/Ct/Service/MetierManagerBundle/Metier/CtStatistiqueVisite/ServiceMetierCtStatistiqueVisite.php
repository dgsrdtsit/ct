<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtStatistiqueVisite;

use Ct\Service\MetierManagerBundle\Entity\CtVisite;
use Ct\Service\MetierManagerBundle\Metier\CtCentre\ServiceMetierCtCentre;
use Ct\Service\MetierManagerBundle\Utils\BilanVisite;
use Ct\Service\MetierManagerBundle\Utils\PathReportingName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtStatistiqueVisite
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
     * Récupérer la liste visite par type (payant, gratuit, apte, inapte)
     * @param string $_type
     * @param integer $_id_centre
     * @param integer $_annee
     * @return array
     */
    public function getListVisiteByType($_type, $_id_centre, $_annee)
    {
        $_sql_type = '';
        if ($_type == 'payant')
            $_sql_type = 'ut.id = 2 AND';
        if ($_type == 'gratuit')
            $_sql_type = 'ut.id = 1 AND';
        if ($_type == 'apte')
            $_sql_type = 'v.vstIsApte = 1 AND';
        if ($_type == 'inapte')
            $_sql_type = 'v.vstIsApte = 0 AND';
        if ($_type == 'total') {
            $_sql_type .= '(ut.id = 2 OR ';
            $_sql_type .= 'ut.id = 1 OR ';
            $_sql_type .= 'v.vstIsApte = 1 OR ';
            $_sql_type .= 'v.vstIsApte = 0) AND';
        }

        $_visite = EntityName::CT_VISITE;
        $_type_usage = EntityName::CT_TYPE_USAGE;
        $_dql = "SELECT tu.id, tu.tpuLibelle as libelle,
                        (SELECT count(v.id) FROM $_visite v
                        LEFT JOIN v.ctCentre ctr
                        LEFT JOIN v.ctUtilisation ut
                        LEFT JOIN v.ctUsage us
                        LEFT JOIN us.ctTypeUsage tus
                        WHERE $_sql_type
                        tus.id = tu.id
                        AND ctr.id = :id_centre
                        AND YEAR(v.vstCreated) = :annee
                        GROUP BY tus.id) AS total
                        FROM $_type_usage tu
                        ORDER BY tu.id ASC";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('annee', $_annee);
        $_query->setParameter('id_centre', $_id_centre);

        return $_query->getResult();
    }

    /**
     * Récupérer la totale visite par type (payant, gratuit, apte, inapte)
     * @param string $_type
     * @param integer $_id_centre
     * @param integer $_annee
     * @return array
     */
    public function getTotalVisiteByType($_type, $_id_centre, $_annee)
    {
        $_sql_type = '';
        if ($_type == 'payant')
            $_sql_type = 'AND ut.id = 2';
        if ($_type == 'gratuit')
            $_sql_type = 'AND ut.id = 1';
        if ($_type == 'apte')
            $_sql_type = 'AND v.vstIsApte = 1';
        if ($_type == 'inapte')
            $_sql_type = 'AND v.vstIsApte = 0';

        $_visite = EntityName::CT_VISITE;
        $_dql = "SELECT count(v.id) as total FROM $_visite v
                    LEFT JOIN v.ctUtilisation ut
                    LEFT JOIN v.ctCentre ctr
                    WHERE ctr.id = :id_centre
                    AND YEAR(v.vstCreated) = :annee
                    $_sql_type";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('annee', $_annee);
        $_query->setParameter('id_centre', $_id_centre);

        return $_query->getSingleResult();
    }

    /**
     * Générer un reporting annuel
     * @param integer $_id_centre
     * @param integer $_annee
     * @return string
     */
    public function generateReportingAnnuel($_id_centre, $_annee)
    {
        // Récupérer manager
        $_centre_manager = $this->_container->get(ServiceName::SRV_METIER_CENTRE);

        // Récupération informations
        // Centre et province
        $_centre        = $_centre_manager->getCtCentreById($_id_centre);
        $_nom_centre    = $_centre->getCtrNom();
        $_code_centre   = $_centre->getCtrCode();
        $_nom_province  = $_centre->getCtProvince()->getPrvNom();
        $_code_province = $_centre->getCtProvince()->getPrvCode();

        // Récupérer répertoire modèle Word
        $_reporting_directory = $this->_container->getParameter('reporting_template_directory');
        $_template_bilan      = $_reporting_directory . PathReportingName::TEMPLATE_VISITE_BILAN_ANNUEL;
        $_num_pv              = 'bilan_annuel_' . $_nom_centre . '_' . $_code_centre . '_' . $_code_province . '_' . $_annee;
        $_filename            = strtoupper($_num_pv);
        $_dest_tmp            = $_reporting_directory . PathReportingName::GENERATE_VISITE_BILAN_ANNUEL . $_filename . '.docx';
        $_url_scheme          = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path                = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_BILAN_ANNUEL . $_filename . '.docx';

        $_php_word = new PhpWord();
        $_template = $_php_word->loadTemplate($_template_bilan);

        // Centre et province
        $_template->setValue('province', $_nom_province);
        $_template->setValue('centre', $_nom_centre);

        // Type
        $_types = $this->getListVisiteByType('payant', $_id_centre, $_annee);
        foreach ($_types as $_key => $_value) {
            $_template->setValue('t#' . $_key, $_value['libelle']);
        }

        // Payant
        $_payants = $this->getListVisiteByType('payant', $_id_centre, $_annee);
        foreach ($_payants as $_key => $_value) {
            $_total = empty($_value['total']) ? 0 : $_value['total'];
            $_template->setValue('p#' . $_key, $_total);
        }

        // Gratuit
        $_gratuits = $this->getListVisiteByType('gratuit', $_id_centre, $_annee);
        foreach ($_gratuits as $_key => $_value) {
            $_total = empty($_value['total']) ? 0 : $_value['total'];
            $_template->setValue('g#' . $_key, $_total);
        }

        // Apte
        $_aptes = $this->getListVisiteByType('apte', $_id_centre, $_annee);
        foreach ($_aptes as $_key => $_value) {
            $_total = empty($_value['total']) ? 0 : $_value['total'];
            $_template->setValue('a#' . $_key, $_total);
        }

        // Inapte
        $_inaptes = $this->getListVisiteByType('inapte', $_id_centre, $_annee);
        foreach ($_inaptes as $_key => $_value) {
            $_total = empty($_value['total']) ? 0 : $_value['total'];
            $_template->setValue('i#' . $_key, $_total);
        }

        // Total par genre
        $_totales = $this->getListVisiteByType('total', $_id_centre, $_annee);
        foreach ($_totales as $_key => $_value) {
            $_total = empty($_value['total']) ? 0 : $_value['total'];
            $_template->setValue('c#' . $_key, $_total);
        }

        // Total par type
        $_totale_payant = $this->getTotalVisiteByType('payant', $_id_centre, $_annee);
        $_totale_gratuit = $this->getTotalVisiteByType('gratuit', $_id_centre, $_annee);
        $_totale_apte = $this->getTotalVisiteByType('apte', $_id_centre, $_annee);
        $_totale_inapte = $this->getTotalVisiteByType('inapte', $_id_centre, $_annee);
        $_template->setValue('pt', $_totale_payant['total']);
        $_template->setValue('gt', $_totale_gratuit['total']);
        $_template->setValue('at', $_totale_apte['total']);
        $_template->setValue('it', $_totale_inapte['total']);

        // Total visite
        $_totale_visite = $this->getTotalVisiteByType('visite', $_id_centre, $_annee);
        $_template->setValue('t', $_totale_visite['total']);

        $_template->saveAs($_dest_tmp);

        return $_path;
    }

    /**
     * Récupérer la liste visite par mois
     * @param integer $_mois
     * @param integer $_id_centre
     * @param integer $_annee
     * @return array
     */
    public function getListVisiteByMois($_mois, $_id_centre, $_annee)
    {
        $_visite = EntityName::CT_VISITE;
        $_type_usage = EntityName::CT_TYPE_USAGE;
        $_dql = "SELECT tu.id, tu.tpuLibelle as libelle,
                        (SELECT count(v.id) FROM $_visite v
                        LEFT JOIN v.ctCentre ctr
                        LEFT JOIN v.ctUtilisation ut
                        LEFT JOIN v.ctUsage us
                        LEFT JOIN us.ctTypeUsage tus
                        WHERE tus.id = tu.id
                        AND ctr.id = :id_centre
                        AND (YEAR(v.vstCreated) = :annee AND MONTH(v.vstCreated) = :mois)
                        GROUP BY tus.id) AS total
                        FROM $_type_usage tu
                        ORDER BY tu.id ASC";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('annee', $_annee);
        $_query->setParameter('mois', $_mois);
        $_query->setParameter('id_centre', $_id_centre);

        return $_query->getResult();
    }

    /**
     * Récupérer le totale visite par mois
     * @param integer $_mois
     * @param integer $_id_centre
     * @param integer $_annee
     * @return array
     */
    public function getTotalVisiteByMois($_mois, $_id_centre, $_annee)
    {
        $_visite = EntityName::CT_VISITE;
        $_dql = "SELECT count(v.id) as total FROM $_visite v
                    LEFT JOIN v.ctUtilisation ut
                    LEFT JOIN v.ctCentre ctr
                    WHERE ctr.id = :id_centre
                    AND (YEAR(v.vstCreated) = :annee AND MONTH(v.vstCreated) = :mois)";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('annee', $_annee);
        $_query->setParameter('mois', $_mois);
        $_query->setParameter('id_centre', $_id_centre);

        return $_query->getSingleResult();
    }

    /**
     * Générer un reporting mensuel
     * @param integer $_id_centre
     * @param integer $_annee
     * @return string
     */
    public function generateReportingMensuel($_id_centre, $_annee)
    {
        // Récupérer manager
        $_centre_manager = $this->_container->get(ServiceName::SRV_METIER_CENTRE);

        // Récupération informations
        // Centre et province
        $_centre        = $_centre_manager->getCtCentreById($_id_centre);
        $_nom_centre    = $_centre->getCtrNom();
        $_code_centre   = $_centre->getCtrCode();
        $_nom_province  = $_centre->getCtProvince()->getPrvNom();
        $_code_province = $_centre->getCtProvince()->getPrvCode();

        // Récupérer répertoire modèle Word
        $_reporting_directory = $this->_container->getParameter('reporting_template_directory');
        $_template_bilan      = $_reporting_directory . PathReportingName::TEMPLATE_VISITE_BILAN_MENSUEL;
        $_num_pv              = 'bilan_mensuel_' . $_nom_centre . '_' . $_code_centre . '_' . $_code_province . '_' . $_annee;
        $_filename            = strtoupper($_num_pv);
        $_dest_tmp            = $_reporting_directory . PathReportingName::GENERATE_VISITE_BILAN_MENSUEL . $_filename . '.docx';
        $_url_scheme          = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path                = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_BILAN_MENSUEL . $_filename . '.docx';

        $_php_word = new PhpWord();
        $_template = $_php_word->loadTemplate($_template_bilan);

        // Centre et province
        $_template->setValue('province', $_nom_province);
        $_template->setValue('centre', $_nom_centre);
        $_template->setValue('an', $_annee);

        // Type
        $_types = $this->getListVisiteByType('payant', $_id_centre, $_annee);
        foreach ($_types as $_key => $_value) {
            $_template->setValue('t#' . $_key, $_value['libelle']);
        }

        // Mois
        $_months = array('jan', 'fev', 'mar', 'avr', 'mai', 'jui', 'jul', 'aou', 'sep', 'oct', 'nov', 'dec');

        // Total par mois
        foreach ($_months as $_key_month => $_month) {
            ++$_key_month;
            $_mois = $this->getListVisiteByMois($_key_month, $_id_centre, $_annee);
            foreach ($_mois as $_key => $_value) {
                $_total = empty($_value['total']) ? 0 : $_value['total'];
                $_template->setValue($_month . '#' . $_key, $_total);
            }
        }

        // Total par genre
        $_totales = $this->getListVisiteByType('total', $_id_centre, $_annee);
        foreach ($_totales as $_key => $_value) {
            $_total = empty($_value['total']) ? 0 : $_value['total'];
            $_template->setValue('c#' . $_key, $_total);
        }

        // Total par type
        foreach ($_months as $_key_month => $_month) {
            ++$_key_month;
            $_mois = $this->getTotalVisiteByMois($_key_month, $_id_centre, $_annee);
            $_total = empty($_mois['total']) ? 0 : $_mois['total'];
            $_template->setValue('t' . $_month, $_total);
        }

        // Total visite
        $_totale_visite = $this->getTotalVisiteByType('visite', $_id_centre, $_annee);
        $_template->setValue('t', $_totale_visite['total']);

        $_template->saveAs($_dest_tmp);

        return $_path;
    }

    /**
     * Récupérer la liste visite par trimestre
     * @param integer $_trimestre
     * @param integer $_id_centre
     * @param integer $_annee
     * @return array
     */
    public function getListVisiteByTrimestre($_trimestre, $_id_centre, $_annee)
    {
        if ($_trimestre == 1)
            $_where_month = '1, 2, 3';
        if ($_trimestre == 2)
            $_where_month = '4, 5, 6';
        if ($_trimestre == 3)
            $_where_month = '7, 8, 9';
        if ($_trimestre == 4)
            $_where_month = '10, 11, 12';

        $_visite = EntityName::CT_VISITE;
        $_type_usage = EntityName::CT_TYPE_USAGE;
        $_dql = "SELECT tu.id, tu.tpuLibelle as libelle,
                        (SELECT count(v.id) FROM $_visite v
                        LEFT JOIN v.ctCentre ctr
                        LEFT JOIN v.ctUtilisation ut
                        LEFT JOIN v.ctUsage us
                        LEFT JOIN us.ctTypeUsage tus
                        WHERE tus.id = tu.id
                        AND ctr.id = :id_centre
                        AND (YEAR(v.vstCreated) = :annee AND MONTH(v.vstCreated) IN ($_where_month))
                        GROUP BY tus.id) AS total
                        FROM $_type_usage tu
                        ORDER BY tu.id ASC";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('annee', $_annee);
        $_query->setParameter('id_centre', $_id_centre);

        return $_query->getResult();
    }

    /**
     * Récupérer le totale visite par trimestre
     * @param integer $_trimestre
     * @param integer $_id_centre
     * @param integer $_annee
     * @return array
     */
    public function getTotalVisiteByTrimestre($_trimestre, $_id_centre, $_annee)
    {
        if ($_trimestre == 1)
            $_where_month = '1, 2, 3';
        if ($_trimestre == 2)
            $_where_month = '4, 5, 6';
        if ($_trimestre == 3)
            $_where_month = '7, 8, 9';
        if ($_trimestre == 4)
            $_where_month = '10, 11, 12';

        $_visite = EntityName::CT_VISITE;
        $_dql = "SELECT count(v.id) as total FROM $_visite v
                    LEFT JOIN v.ctUtilisation ut
                    LEFT JOIN v.ctCentre ctr
                    WHERE ctr.id = :id_centre
                    AND (YEAR(v.vstCreated) = :annee AND MONTH(v.vstCreated) IN ($_where_month))";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('annee', $_annee);
        $_query->setParameter('id_centre', $_id_centre);

        return $_query->getSingleResult();
    }

    /**
     * Générer un reporting trimestriel
     * @param integer $_id_centre
     * @param integer $_annee
     * @return string
     */
    public function generateReportingTrimestriel($_id_centre, $_annee)
    {
        // Récupérer manager
        $_centre_manager = $this->_container->get(ServiceName::SRV_METIER_CENTRE);

        // Récupération informations
        // Centre et province
        $_centre        = $_centre_manager->getCtCentreById($_id_centre);
        $_nom_centre    = $_centre->getCtrNom();
        $_code_centre   = $_centre->getCtrCode();
        $_nom_province  = $_centre->getCtProvince()->getPrvNom();
        $_code_province = $_centre->getCtProvince()->getPrvCode();

        // Récupérer répertoire modèle Word
        $_reporting_directory = $this->_container->getParameter('reporting_template_directory');
        $_template_bilan      = $_reporting_directory . PathReportingName::TEMPLATE_VISITE_BILAN_TRIMESTRIEL;
        $_num_pv              = 'bilan_trimestriel_' . $_nom_centre . '_' . $_code_centre . '_' . $_code_province . '_' . $_annee;
        $_filename            = strtoupper($_num_pv);
        $_dest_tmp            = $_reporting_directory . PathReportingName::GENERATE_VISITE_BILAN_TRIMESTRIEL . $_filename . '.docx';
        $_url_scheme          = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path                = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_VISITE_BILAN_TRIMESTRIEL . $_filename . '.docx';

        $_php_word = new PhpWord();
        $_template = $_php_word->loadTemplate($_template_bilan);

        // Centre et province
        $_template->setValue('province', $_nom_province);
        $_template->setValue('centre', $_nom_centre);
        $_template->setValue('an', $_annee);

        // Type
        $_types = $this->getListVisiteByType('payant', $_id_centre, $_annee);
        foreach ($_types as $_key => $_value) {
            $_template->setValue('t#' . $_key, $_value['libelle']);
        }

        // Trimestriels
        $_trimestriels = array('jm', 'aj', 'js', 'od');

        // Total par trimestre
        foreach ($_trimestriels as $_key_trimestriel => $_trimestriel) {
            ++$_key_trimestriel;
            $_trimestre = $this->getListVisiteByTrimestre($_key_trimestriel, $_id_centre, $_annee);
            foreach ($_trimestre as $_key => $_value) {
                $_total = empty($_value['total']) ? 0 : $_value['total'];
                $_template->setValue($_trimestriel . '#' . $_key, $_total);
            }
        }

        // Total par genre
        $_totales = $this->getListVisiteByType('total', $_id_centre, $_annee);
        foreach ($_totales as $_key => $_value) {
            $_total = empty($_value['total']) ? 0 : $_value['total'];
            $_template->setValue('c#' . $_key, $_total);
        }

        // Total par type
        foreach ($_trimestriels as $_key_trimestriel => $_trimestriel) {
            ++$_key_trimestriel;
            $_trimestre = $this->getTotalVisiteByMois($_key_trimestriel, $_id_centre, $_annee);
            $_total = empty($_trimestre['total']) ? 0 : $_trimestre['total'];
            $_template->setValue('t' . $_trimestriel, $_total);
        }

        // Total visite
        $_totale_visite = $this->getTotalVisiteByType('visite', $_id_centre, $_annee);
        $_template->setValue('t', $_totale_visite['total']);

        $_template->saveAs($_dest_tmp);

        return $_path;
    }

    /*
     * Statistique journalier
     * @param $_id_centre
     * @param $_id_user
     */
    public function getAllStatistique($_id_centre, $_is_centre)
    {
        $_today_date = date('Y-m-d', time() - 3600 * 24);
        $_dql_centre = $_is_centre ? " ctr.id = :id_centre AND " : "";
        $_visite = EntityName::CT_VISITE;
        $_carte_grise = EntityName::CT_CARTE_GRISE;
        $_reception = EntityName::CT_RECEPTION;
        $_cad = EntityName::CT_CONST_AV_DED;
        $_dql_visite = "SELECT count(v.id) as total FROM $_visite v
                    LEFT JOIN v.ctCentre ctr
                    WHERE " . $_dql_centre . " v.vstIsContreVisite = false AND v.vstCreated > :today ";

        $_query = $this->_entity_manager->createQuery($_dql_visite);
        $_query->setParameter('today', $_today_date);
        if ($_is_centre)
            $_query->setParameter('id_centre', $_id_centre);

        $_visite_res = $_query->getSingleResult();
        $_stat_visite = $_visite_res['total'];

        $_dql_visite = "SELECT count(v.id) as total FROM $_visite v
                    LEFT JOIN v.ctCentre ctr
                    WHERE " . $_dql_centre . " v.vstIsContreVisite = true AND v.vstCreated > :today ";

        $_query = $this->_entity_manager->createQuery($_dql_visite);
        $_query->setParameter('today', $_today_date);
        if ($_is_centre)
            $_query->setParameter('id_centre', $_id_centre);
        $_visite_contre = $_query->getSingleResult();
        $_stat_contre_visite = $_visite_contre['total'];

        $_dql_carte_grise = "SELECT count(c.id) as total FROM $_carte_grise c
                    LEFT JOIN c.ctCentre ctr
                    WHERE  " . $_dql_centre . " c.cgCreated > :today ";
        $_query = $this->_entity_manager->createQuery($_dql_carte_grise);
         $_query->setParameter('today', $_today_date);
        if ($_is_centre)
            $_query->setParameter('id_centre', $_id_centre);
        $_carte_grise = $_query->getSingleResult();
        $_stat_carte_grise = $_carte_grise['total'];

        $_dql_reception = "SELECT count(r.id) as total FROM $_reception r
                    LEFT JOIN r.ctCentre ctr
                    WHERE " . $_dql_centre . " r.rcpCreated > :today ";
        $_query = $this->_entity_manager->createQuery($_dql_reception);
        $_query->setParameter('today', $_today_date);
        if ($_is_centre)
            $_query->setParameter('id_centre', $_id_centre);
        $_reception = $_query->getSingleResult();
        $_stat_reception = $_reception['total'];

        $_dql_cad = "SELECT count(cad.id) as total FROM $_cad cad
                     LEFT JOIN cad.ctCentre ctr
                     WHERE " . $_dql_centre . " cad.cadCreated > :today ";
        $_query = $this->_entity_manager->createQuery($_dql_cad);
        $_query->setParameter('today', $_today_date);
        if ($_is_centre)
            $_query->setParameter('id_centre', $_id_centre);
        $_cad      = $_query->getSingleResult();
        $_stat_cad = $_cad['total'];

        $_statistique = array(
            'stat_visite'        => $_stat_visite,
            'stat_contre_visite' => $_stat_contre_visite,
            'stat_carte_grise'   => $_stat_carte_grise,
            'stat_reception'     => $_stat_reception,
            'stat_cad'           => $_stat_cad
        );
        return $_statistique;

    }

    /**
     * Récupérer la liste visite par type (payant, gratuit)
     * @param string $_type_util Type utilisation
     * @param integer $_id_centre
     * @param integer $_annee
     * @param integer $_value
     * @param string $_interval
     * @param string $_rec_type
     * @return array
     */
    public function getListVisite($_id_centre, $_annee, $_value, $_interval, $_type_visite)
    {
        $_e_visite       = EntityName::CT_VISITE;
        $_e_usage        = EntityName::CT_USAGE;

        $_sql_type = '';
        if ($_type_visite == 'apte')
            $_sql_type = 'AND v.vstIsApte = 1';
        elseif ($_type_visite == 'inapte')
            $_sql_type = 'AND v.vstIsApte = 0';


        if ($_interval == "mensuel") {
            $_and_stmt = "AND MONTH(v.vstCreated) = :mois ";
        } elseif ($_interval == "trimestriel") {
            if ($_value == 1)
                $_where_month = '1, 2, 3';
            if ($_value == 2)
                $_where_month = '4, 5, 6';
            if ($_value == 3)
                $_where_month = '7, 8, 9';
            if ($_value == 4)
                $_where_month = '10, 11, 12';
            $_and_stmt = "AND MONTH(v.vstCreated) IN ($_where_month) ";
        } elseif ($_interval == "semestriel") {
            if ($_value == 1)
                $_where_month = '1, 2, 3, 4, 5, 6';
            if ($_value == 2)
                $_where_month = '7, 8, 9, 10, 11, 12';
            $_and_stmt = "AND MONTH(v.vstCreated) IN ($_where_month) ";
        } else {
            $_and_stmt = " ";
        }

        $_dql = "SELECT us.id, us.usgLibelle as libelle,
                (SELECT count(v.id) FROM $_e_visite v
                LEFT JOIN v.ctCentre ctr
                LEFT JOIN v.ctUsage usg
                WHERE usg.id = us.id
                $_sql_type
                AND ctr.id = :id_centre
                AND YEAR(v.vstCreated) = :annee
                $_and_stmt
                GROUP BY us.id) as total
                FROM $_e_usage us
                ORDER BY us.id ASC";
        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('annee', $_annee);
        $_query->setParameter('id_centre', $_id_centre);
        if ($_interval == "mensuel") {
            $_query->setParameter('mois', $_value);
        }

        return $_query->getResult();
    }

    /**
     * Récupérer la liste visite par usage
     * @param string $_type_util Type utilisation
     * @param integer $_id_centre
     * @param integer $_annee
     * @param integer $_value
     * @param string $_interval
     * @param string $_rec_type
     * @return array
     */
    public function getTotalVisiteByUsage($_id_centre, $_annee, $_value, $_interval, $_type_visite)
    {
        $_list_visites = $this->getListVisite($_id_centre, $_annee, $_value, $_interval, $_type_visite);
        $_list_usages = array("TV","VP","VL","AE","VL < 6 places (VL6)","TM3","REM3","SREM3","TA",
                        "TMTPERS","VP300","TPP","TCS","Tbé","TBR","CU","CSUB","TM6","REM6",
                        "SREM6","TM7","TRR7","REM7","SREM7","TM10","TRR10","REM10","SREM10",
                        "TM16","TRR16","REM16","SREM16");
        $_matches = array();
        foreach ($_list_visites as $_list) {
            $_usage_libelle = $_list["libelle"];
            $_total         = $_list["total"];
            foreach($_list_usages as $k=>$v) {
                if(preg_match("/\b$v\b/i", $_usage_libelle)) {
                    if (array_key_exists($v, $_matches)) {
                        $_matches[$v] = $_matches[$v] + $_total;
                    } else {
                        $_matches[$v] = (int)$_total;
                    }

                }
            }
        }
        $_matches_keys = array_keys($_matches);
        foreach ($_list_usages as $_key) {
            if (!in_array($_key, $_matches_keys)) {
                $_matches[$_key] = 0;
            }
        }
        return $_matches;
    }

    /**
     * Générer un reporting annuel
     * @param integer $_id_centre
     * @param integer $_annee
     * @param string $_type_recep
     * @param string $_type_util
     * @return string
     */
    public function generateReportingByInterval($_id_centre, $_annee, $_value, $_interval)
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

        $_tpl_reporting_name = PathReportingName::TEMPLATE_VISITE_BILAN;
        $_gnr_reporting_name = PathReportingName::GENERATE_VISITE_BILAN;

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
        $_totale_aptes = $this->getTotalVisiteByUsage($_id_centre, $_annee, $_value, $_interval, 'apte');
        $_totale_inaptes = $this->getTotalVisiteByUsage($_id_centre, $_annee, $_value, $_interval, 'inapte');
        $_totales = $this->getTotalVisiteByUsage($_id_centre, $_annee, $_value, $_interval, 'all');

        $_list_mois = array(1 => "JANVIER", 2 => "FÉVRIER", 3 => "MARS",
                            4 => "AVRIL", 5 => "MAI", 6 => "JUIN",
                            7 => "JUILLET", 8 => "AOÛT", 9 => "SEPTEMBRE",
                            10 => "OCTOBRE", 11 => "NOVEMBRE", 12 => "DÉCEMBRE");

        if ($_interval == 'annuel') {
            $_template->setValue('value', '');
            $_template->setValue('rank', '');
            $_template->setValue('interval', 'ANNUEL');
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

        foreach ($_totales as $_key => $_val) {
            if (stripos($_key, 'VL6') !== false) $_libelle = "vl6";
            elseif (stripos($_key, 'tbé') !== false) $_libelle = "tbe";
            else $_libelle = strtolower($_key);
            $_total = empty($_val) ? 0 : $_val;
            $_template->setValue($_libelle . '_tot', $_total);
        }

        foreach ($_totale_aptes as $_key => $_val) {
            if (stripos($_key, 'VL6') !== false) $_libelle = "vl6";
            elseif (stripos($_key, 'tbé') !== false) $_libelle = "tbe";
            else $_libelle = strtolower($_key);
            $_total = empty($_val) ? 0 : $_val;
            $_template->setValue($_libelle . '_apte', $_total);
        }

        foreach ($_totale_inaptes as $_key => $_val) {
            if (stripos($_key, 'VL6') !== false) $_libelle = "vl6";
            elseif (stripos($_key, 'tbé') !== false) $_libelle = "tbe";
            else $_libelle = strtolower($_key);
            $_total = empty($_val) ? 0 : $_val;
            $_template->setValue($_libelle . '_inapte', $_total);
        }

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
