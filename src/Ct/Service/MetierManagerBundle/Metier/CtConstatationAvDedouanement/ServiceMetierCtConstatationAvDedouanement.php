<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtConstatationAvDedouanement;

use Ct\Service\MetierManagerBundle\Entity\CtConstAvDed;
use Ct\Service\MetierManagerBundle\Utils\PathReportingName;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtConstatationAvDedouanement
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
     * Récuperer le repository constatation avant dédouanement
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_CONST_AV_DED);
    }

    /**
     * Récuperer toutes les constatations avant dédouanement
     * @return array
     */
    public function getAllCtConstatationAvDedouanement()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer toutes les constatations avant dédouanement par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtConstatationAvDedouanementByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un type constatation avant dédouanement par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtConstatationAvDedouanementById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer une constatation avant dédouanement
     * @param Entity $_const_av_ded
     * @param string $_action
     * @return boolean
     */
    public function saveCtConstatationAvDedouanement($_const_av_ded, $_action)
    {
        if ($_action == 'new' || $_action == 'update') {
            $this->_entity_manager->persist($_const_av_ded);
            $this->_entity_manager->flush();

            if ($_action == 'update') {
                // Historique modification
                $_historique_manager = $this->_container->get(ServiceName::SRV_METIER_HISTORIQUE);
                $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();
                $_date_time_modif = new \DateTime();
                $_date_time_modif = $_date_time_modif->format("d/m/Y H:i:s");
                $_historique_manager->logActionUser($_user_connected, $_const_av_ded, $_date_time_modif);
            }

            $_id_const_av_ded = $_const_av_ded->getId();

            $_centre_reception = $_const_av_ded->getCtCentre();
            $_province = $_centre_reception->getCtProvince();

            $_code_centre = $_centre_reception->getCtrCode();
            $_code_province = $_province->getPrvCode();
            $_type_const = "CONST";
            $_current_year = date('Y');

            $_num_pv = $_id_const_av_ded . '/CENSERO/' . $_code_province . '/' . $_code_centre . '/' . $_type_const . '/' . $_current_year;
            $_const_av_ded->setCadNumero($_num_pv);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer uneconstatation avant dédouanement
     * @param Entity $_const_av_ded
     * @return boolean
     */
    public function deleteCtConstatationAvDedouanement($_const_av_ded)
    {
        $this->_entity_manager->remove($_const_av_ded);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple de constatation avant dédouanement
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtConstatationAvDedouanement($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_const_av_ded = $this->getCtConstatationAvDedouanementById($_id);
                $this->deleteCtConstatationAvDedouanement($_const_av_ded);
            }
        }

        return true;
    }

    /**
     * Générer PV Constatation
     * @param integer $_id
     * @return string
     */
    public function generatePVConstatation($_id)
    {
        $_constatation = $this->getCtConstatationAvDedouanementById($_id);
        $_cadct_manager = $this->_container->get(ServiceName::SRV_METIER_TYPE_CONST_AV_DED);
        $_ptac_manager        = $this->_container->get(ServiceName::SRV_METIER_DROIT_PTAC);
        $_dest_final = null;
        $_taux_tva = $this->_container->getParameter('taxe');

        if (isset($_constatation) && count($_constatation)) {
            $_num_pv                = $_constatation->getCadNumero();
            $_centre                = $_constatation->getCtCentre()->getCtrNom();
            $_date_created          = $_constatation->getCadCreated()->format('d-m-Y');
            $_verificateur          = $_constatation->getCtVerificateur()->getUsrName();
            $_immatriculation       = $_constatation->getCadImmatriculation();
            $_provenance            = $_constatation->getCadProvenance();
            $_port_embarq           = $_constatation->getCadLieuEmbarquement();
            $_date_embarq           = $_constatation->getCadDateEmbarquement()->format('d-m-Y');
            $_prop_nom              = $_constatation->getCadProprietaireNom();
            $_prop_adrs             = $_constatation->getCadProprietaireAdresse();
            $_divers                = $_constatation->getCadDivers();
            $_conforme              = $_constatation->getCadConforme() == 1 ? "CONFORME" : "NON CONFORME";
            $_bon_etat              = $_constatation->getCadBonEtat() == 1 ? "OUI" : "NON";
            $_sec_pers              = $_constatation->getCadSecPers() == 1 ? "OUI" : "NON";
            $_sec_march             = $_constatation->getCadSecMarch() == 1 ? "OUI" : "NON";
            $_protec_env            = $_constatation->getCadProtecEnv() == 1 ? "OUI" : "NON";
            $_observation           = $_constatation->getCadObservation();

            $_droit                 = null;
            $_d_ptac                = null;
            $_d_genre               = null;

            // Récupérer répertoire modèle Word
            $_pv_directory              = $this->_container->getParameter('reporting_template_directory');

            $_source_pv                 = $_pv_directory . PathReportingName::TEMPLATE_CONSTATATION;
            $_phpWord                   = new PhpWord();
            $_template                  = $_phpWord->loadTemplate($_source_pv);
            $_filename                  = str_replace('/', '_', $_num_pv);

            $_path                      = $_pv_directory . PathReportingName::GENERATE_CONSTATATION;

            $_dest_tmp                  = $_path . $_filename . '.docx';
            $_file_without_ext          = $_filename;

            $_url_scheme                = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
            $_path_docx                 = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_CONSTATATION . $_filename . '.docx';
            $_path_pdf                  = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_CONSTATATION . $_filename . '.pdf';

            $_template->setValue('centre', htmlspecialchars($_centre));
            $_template->setValue('date_created', $_date_created);
            $_template->setValue('num_pv', htmlspecialchars($_num_pv));
            $_template->setValue('nom_verif', htmlspecialchars($_verificateur));
            $_template->setValue('motif', htmlspecialchars($_immatriculation));
            $_template->setValue('provenance', htmlspecialchars($_provenance));
            $_template->setValue('date_embarq', $_date_embarq);
            $_template->setValue('lieu_embarq', htmlspecialchars($_port_embarq));
            
            $_const_caracs          = $_constatation->getCtConstAvDedCaracs();
            if (isset($_const_caracs) && count($_const_caracs) > 1) {
                $_poids_maxima = null;
                $_list_of_keys = array();
                foreach ($_const_caracs as $_key => $_const_carac) {
                    $_key++;
                    $_mise_en_circ       = $_const_carac->getCadPremiereCircule();
                    $_ct_genre           = $_const_carac->getCtGenre();
                    $_genre              = $_const_carac->getCtGenre() ? $_const_carac->getCtGenre()->getGrLibelle() : '-';
                    $_marque             = $_const_carac->getCtMarque() ? $_const_carac->getCtMarque()->getMrqLibelle() : '-';
                    $_type               = $_const_carac->getCadTypeCar();
                    $_num_serie          = $_const_carac->getCadNumSerieType();
                    $_num_moteur         = $_const_carac->getCadNumMoteur();
                    $_source_energie     = $_const_carac->getCtSourceEnergie() ? $_const_carac->getCtSourceEnergie()->getSreLibelle() : '-';
                    $_cylindree          = $_const_carac->getCadCylindre();
                    $_puissance          = $_const_carac->getCadPuissance();
                    $_carosserie         = $_const_carac->getCtCarosserie() ? $_const_carac->getCtCarosserie()->getCrsLibelle() : '-';
                    $_nb_place           = $_const_carac->getCadNbrAssis();
                    $_largeur            = $_const_carac->getCadLargeur();
                    $_longueur           = $_const_carac->getCadLongueur();
                    $_hauteur            = $_const_carac->getCadHauteur();
                    $_charge_utile       = $_const_carac->getCadChargeUtile();
                    $_poids_vide         = $_const_carac->getCadPoidsVide();
                    $_ptac               = $_const_carac->getCadPoidsTotalCharge();
                    $_poids_maxima       = $_const_carac->getCadPoidsMaxima();
                    $_const_carc_type    = $_const_carac->getctConstAvDedType();

                    $_mise_en_circ       = isset($_mise_en_circ)?$_mise_en_circ:"-";
                    $_genre              = isset($_genre)?($_genre):"-";
                    $_marque             = isset($_marque)?$_marque:"-";
                    $_type               = isset($_type)?$_type:"-";
                    $_num_serie          = isset($_num_serie)?$_num_serie:"-";
                    $_num_moteur         = isset($_num_moteur)?$_num_moteur:"-";
                    $_source_energie     = isset($_source_energie)?$_source_energie:"-";
                    $_cylindree          = isset($_cylindree)?$_cylindree:"-";
                    $_puissance          = isset($_puissance)?$_puissance:"-";
                    $_carosserie         = isset($_carosserie)?$_carosserie:"-";
                    $_nb_place           = isset($_nb_place)?$_nb_place:"-";
                    $_largeur            = isset($_largeur)?$_largeur:"-";
                    $_longueur           = isset($_longueur)?$_longueur:"-";
                    $_hauteur            = isset($_hauteur)?$_hauteur:"-";
                    $_charge_utile       = isset($_charge_utile)?$_charge_utile:"-";
                    $_poids_vide         = isset($_poids_vide)?$_poids_vide:"-";
                    $_ptac               = isset($_ptac)?$_ptac:"-";
                    $_poids_maxima       = isset($_poids_maxima)?$_poids_maxima:"-";

                    $_const_carac_noti   = $_cadct_manager->getCtTypeConstatationAvDedouanementByLibelle("notice");
                    $_const_carac_cg     = $_cadct_manager->getCtTypeConstatationAvDedouanementByLibelle("grise");
                    $_const_carac_crp     = $_cadct_manager->getCtTypeConstatationAvDedouanementByLibelle("corps");
                    if ($_const_carc_type->getId() == $_const_carac_noti->getId()) {
                        $_key = 1;
                        array_push($_list_of_keys, $_key);
                    } elseif ($_const_carc_type->getId() == $_const_carac_cg->getId()) {
                        $_key = 2;
                        array_push($_list_of_keys, $_key);
                    } elseif ($_const_carc_type->getId() == $_const_carac_crp->getId()) {
                        $_key = 3;
                        array_push($_list_of_keys, $_key);
                        $_d_ptac = $_ptac;
                        $_d_genre = $_ct_genre;
                    }
                    

                    $_template->setValue('mise_en_circ_' . $_key, $_mise_en_circ);
                    $_template->setValue('genre_' . $_key, htmlspecialchars($_genre));
                    $_template->setValue('marque_' . $_key, htmlspecialchars($_marque));
                    $_template->setValue('serie_type_' . $_key, htmlspecialchars($_type));
                    $_template->setValue('num_serie_' . $_key, htmlspecialchars($_num_serie));
                    $_template->setValue('num_moteur_' . $_key, htmlspecialchars($_num_moteur));
                    $_template->setValue('source_' . $_key, $_source_energie);
                    $_template->setValue('cylindre_' . $_key, $_cylindree);
                    $_template->setValue('puissance_' . $_key, $_puissance);
                    $_template->setValue('carrosserie_' . $_key, $_carosserie);
                    $_template->setValue('assise_' . $_key, $_nb_place);
                    $_template->setValue('largeur_' . $_key, $_largeur);
                    $_template->setValue('hauteur_' . $_key, $_hauteur);
                    $_template->setValue('longueur_' . $_key, $_longueur);
                    $_template->setValue('cu_' . $_key, $_charge_utile);
                    $_template->setValue('poids_vide_' . $_key, $_poids_vide);
                    $_template->setValue('ptc_' . $_key, $_ptac);
                }

                for ($_key = 1; $_key < 4; $_key++) {
                    if (!in_array($_key, $_list_of_keys)) {
                        $_template->setValue('mise_en_circ_' . $_key, "-");
                        $_template->setValue('genre_' . $_key, "-");
                        $_template->setValue('marque_' . $_key, "-");
                        $_template->setValue('serie_type_' . $_key, "-");
                        $_template->setValue('num_serie_' . $_key, "-");
                        $_template->setValue('num_moteur_' . $_key, "-");
                        $_template->setValue('source_' . $_key, "-");
                        $_template->setValue('cylindre_' . $_key, "-");
                        $_template->setValue('puissance_' . $_key, "-");
                        $_template->setValue('carrosserie_' . $_key, "-");
                        $_template->setValue('assise_' . $_key, "-");
                        $_template->setValue('largeur_' . $_key, "-");
                        $_template->setValue('hauteur_' . $_key, "-");
                        $_template->setValue('longueur_' . $_key, "-");
                        $_template->setValue('cu_' . $_key, "-");
                        $_template->setValue('poids_vide_' . $_key, "-");
                        $_template->setValue('ptc_' . $_key, "-");
                    }

                }

                $_droit_f   = 0;
                $_tht_f     = 0;
                $_tva_f     = 0;
                $_montant_f = 0;
                $_pv_f      = 0;

                $_ptac_tonne      = $_d_ptac / 1000; // Convertir en tonne
                $_categorie_genre = $_d_genre->getCtGenreCategorie();
                $_droit           = $_ptac_manager->getDroitByCategorieGenreEtPtac($_categorie_genre, $_ptac_tonne, 'constatation');
                if ($_droit) {
                    $_droit_f = number_format($_droit, 0, ',', ' ');
                    $_pv        = 2000;
                    $_tht       = $_pv + $_droit;
                    $_tva       = $_tht * $_taux_tva;
                    $_timbre    = 0; // Je sais pas la valeur
                    $_montant   = $_tht + $_tva + $_timbre;
                    $_tht_f     = number_format($_tht, 0, ',', ' ');
                    $_pv_f      = number_format($_pv, 0, ',', ' ');
                    $_tva_f     = number_format($_tva, 0, ',', ' ');
//                    $_timbre_f  = number_format($_timbre, 0, ',', ' ');
                    $_montant_f = number_format($_montant, 0, ',', ' ');
                }
                $_template->setValue('droit', $_droit_f);
                $_template->setValue('tht', $_tht_f);
                $_template->setValue('tva', $_tva_f);
                $_template->setValue('montant', $_montant_f);
                $_template->setValue('pv', $_pv_f);

                $_template->setValue('observation', htmlspecialchars($_observation));
                $_template->setValue('divers', $_divers);
                $_template->setValue('proprietaire', htmlspecialchars($_prop_nom));
                $_template->setValue('adresse', $_prop_adrs);
                $_template->setValue('conformite', $_conforme);
                $_template->setValue('bon_etat', $_bon_etat);
                $_template->setValue('securite_personne', $_sec_pers);
                $_template->setValue('securite_marchandise', $_sec_march);
                $_template->setValue('protection_environnement', $_protec_env);

                $_template->saveAs($_dest_tmp);

                $_dest_final = $this->convertToPdf($_path, $_file_without_ext);
            }

            return array(
                'download_path' => $_dest_final,
                'url_path'      => $_path_pdf
            );

        }
//        return $_dest_final;
    }

    /**
     * Convertir .docx en pdf (utilisant libreoffice command)
     * @param string $_path
     * @param string $_filename
     * @return string
     */
    public function convertToPdf($_path, $_file_without_ext) {
        $_file_docx = $_file_without_ext . ".docx";
        $_file_pdf = $_file_without_ext . ".pdf";
        $_source_tmp = $_path . $_file_docx;
        $_dest_tmp = $_path . $_file_pdf;
        $_libreoffice_path = $this->_container->getParameter('libreoffice_path');
        $_command_to_execute = $_libreoffice_path . " " . $_source_tmp . " --outdir " . $_path;
        exec($_command_to_execute, $_output, $_return_var);
        if (file_exists($_dest_tmp) && file_exists($_source_tmp)) {
            unlink($_source_tmp);
            return $_dest_tmp;
        }
        return false;
    }

    /**
     * Générer PV Constatation lien de telechargement
     * @param integer $_id
     * @return array
     */
    public function generatePVConstatationDownloadLink($_id)
    {
        $_ext_docx  = ".docx";
        $_ext_pdf   = ".pdf";
        $_ext = $_ext_pdf;
        $_const_av_ded = $this->getCtConstatationAvDedouanementById($_id);
        $_num_pv = $_const_av_ded->getCadNumero();
        $_num_pv = str_replace('/', '_', $_num_pv);
        $_filename = $_num_pv . $_ext;
        $_path = $this->_container->get('kernel')->getRootDir(). "/../web/reporting/" . PathReportingName::GENERATE_CONSTATATION;
        return array(
            'filename' => $_filename,
            'path' => $_path
        );

    }

    /**
     * Récuperer tout les constatation par centre
     * @return array
     */
    public function getAllCtConstatationByCentreAndDate($_centre_id, $_date)
    {
        $_entity_v = EntityName::CT_CONST_AV_DED;
        $_dql = "SELECT v FROM $_entity_v v
                 LEFT JOIN v.ctCentre c
                 WHERE c.id = :id
                 AND v.cadCreated LIKE :date
                 ORDER BY v.cadCreated DESC";
        $_query     = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('id', $_centre_id);
        $_query->setParameter('date', '%'.$_date.'%');
        return $_query->getResult();
    }

    /**
     * Générer un feuille de caisse
     * @param string $_date
     * @return string
     */
    public function generateFeuilleCaisse($_date, $_centre_id) {
        // Récupérer manager
        $_cadct_manager         = $this->_container->get(ServiceName::SRV_METIER_TYPE_CONST_AV_DED);
        $_ptac_manager          = $this->_container->get(ServiceName::SRV_METIER_DROIT_PTAC);
        $_dest_final            = null;
        $_taux_tva              = $this->_container->getParameter('taxe');
        $_centre_manager        = $this->_container->get(ServiceName::SRV_METIER_CENTRE);

        if ($_centre_id > 0) {
            $_centre = $_centre_manager->getCtCentreById($_centre_id);
        } else {
            // Récupérer le centre de l'utilisateur connecté
            $_user_connected = $this->_container->get('security.token_storage')->getToken()->getUser();
            $_centre         = $_user_connected->getCtCentre();
        }
        $_centre_id     = $_centre->getId();

        // Récupération informations
        // Centre et province
        $_nom_centre    = $_centre->getCtrNom();
        $_code_centre   = $_centre->getCtrCode();
        $_nom_province  = $_centre->getCtProvince()->getPrvNom();
        $_code_province = $_centre->getCtProvince()->getPrvCode();


        // Récupérer répertoire modèle Word
        $_pv_directory          = $this->_container->getParameter('reporting_template_directory');
        $_template_src          = $_pv_directory . PathReportingName::TEMPLATE_FDC_CONSTATATION;
        $_date_parsed           = str_replace('-', '_', $_date);
        $_path                  = $_pv_directory . PathReportingName::GENERATE_CONSTATATION;
      
        $_num_pv                = 'feuille_de_caisse_' . $_code_centre . '_' . $_code_province . '_' . $_date_parsed;
        $_filename              = strtoupper($_num_pv);

        $_dest_tmp              = $_path . $_filename . '.docx';
        $_file_without_ext      = $_filename;

        $_url_scheme            = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path_docx             = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_CONSTATATION . $_filename . '.docx';
        $_path_pdf              = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_CONSTATATION . $_filename . '.pdf';

        $_php_word = new PhpWord();
        $_template = $_php_word->loadTemplate($_template_src);

        // Centre et province
        $_template->setValue('province', $_nom_province);
        $_template->setValue('centre', $_nom_centre);

        // Constatation
        $_constatations       = $this->getAllCtConstatationByCentreAndDate($_centre_id, $_date);
        $_nbr_constatation    = count($_constatations);
        $_total_droit   = 0;
        $_total_pv      = 0;
        $_total_tht     = 0;
        $_total_taxe    = 0;
        $_total_montant = 0;
        $_template->cloneRow('numpv', $_nbr_constatation);

        foreach ($_constatations as $_key => $_constatation) {
            ++$_key;

            $_pv        = 0;
            $_tht       = 0;
            $_tva       = 0;
            $_montant   = 0;
            $_droit_f   = 0;
            $_pv_f      = 0;
            $_tht_f     = 0;
            $_tva_f     = 0;
            $_montant_f = 0;

            $_num_pv                = $_constatation->getCadNumero();
            $_prop_nom              = $_constatation->getCadProprietaireNom();

            $_const_caracs          = $_constatation->getCtConstAvDedCaracs();
            if (isset($_const_caracs) && count($_const_caracs) > 1) {
                $_poids_maxima = null;
                foreach ($_const_caracs as $_const_carac) {
                    $_ct_genre = $_const_carac->getCtGenre();
                    $_marque = $_const_carac->getCtMarque() ? $_const_carac->getCtMarque()->getMrqLibelle() : '';
                    $_ptac = $_const_carac->getCadPoidsTotalCharge();
                    $_const_carc_type = $_const_carac->getctConstAvDedType();

                    $_ptac = isset($_ptac) ? $_ptac : "";

                    $_const_carac_crp = $_cadct_manager->getCtTypeConstatationAvDedouanementByLibelle("corps");
                    if ($_const_carc_type->getId() == $_const_carac_crp->getId()) {
                        $_d_ptac = $_ptac;
                        $_d_genre = $_ct_genre;
                        $_d_marque = $_marque;
                    }
                }
            }

            $_ptac_tonne        = $_d_ptac / 1000; // Convertir en tonne
            $_categorie_genre   = $_d_genre->getCtGenreCategorie();
            $_droit             = $_ptac_manager->getDroitByCategorieGenreEtPtac($_categorie_genre, $_ptac_tonne, 'constatation');
            if ($_droit) {
                $_droit_f = number_format($_droit, 0, ',', ' ');
                $_pv = 2000;
                $_tht = $_pv + $_droit;
                $_tva = $_tht * $_taux_tva;
                $_timbre = 0; // Je sais pas la valeur
                $_montant = $_tht + $_tva + $_timbre;
                $_tht_f = number_format($_tht, 0, ',', ' ');
                $_pv_f = number_format($_pv, 0, ',', ' ');
                $_tva_f = number_format($_tva, 0, ',', ' ');
                $_montant_f = number_format($_montant, 0, ',', ' ');
            }

            // Total montant vertical
            $_total_droit   += $_droit;
            $_total_pv      += $_pv;
            $_total_tht     += $_tht;
            $_total_taxe    += $_tva;
            $_total_montant += $_montant;

            $_template->setValue('numpv#' . $_key, $_num_pv);
            $_template->setValue('proprietaire#' . $_key, htmlspecialchars($_prop_nom));
            $_template->setValue('marque#' . $_key, $_d_marque);
            $_template->setValue('genre#' . $_key, $_d_genre->getGrLibelle());
            $_template->setValue('ptc#' . $_key, $_d_ptac);
            $_template->setValue('droit#' . $_key, $_droit_f);
            $_template->setValue('pv#' . $_key, $_pv_f);
            $_template->setValue('tht#' . $_key, $_tht_f);
            $_template->setValue('taxe#' . $_key, $_tva_f);
            $_template->setValue('montant#' . $_key, $_montant_f);


        }

        $_total_droit_f      = number_format($_total_droit, 0, ',', ' ');
        $_total_pv_f         = number_format($_total_pv, 0, ',', ' ');
        $_total_tht_f         = number_format($_total_tht, 0, ',', ' ');
        $_total_taxe_f       = number_format($_total_taxe, 0, ',', ' ');
        $_total_montant_f    = number_format($_total_montant, 0, ',', ' ');

        // Formattage date
        $_date_exploded = explode('-', $_date);
        $_date_parsed   = $_date_exploded[2] . '/' . $_date_exploded[1] . '/' . $_date_exploded[0];

        $_template->setValue('date', $_date_parsed);
        $_template->setValue('nb_constatation', $_nbr_constatation);
        $_template->setValue('droit_total', $_total_droit_f);
        $_template->setValue('pv_total', $_total_pv_f);
        $_template->setValue('tht_total', $_total_tht_f);
        $_template->setValue('taxe_total', $_total_taxe_f);
        $_template->setValue('montant_total', $_total_montant_f);

        $_template->saveAs($_dest_tmp);

        $_dest_tmp = $this->convertToPdf($_path, $_file_without_ext);


        return array(
            'download_path' => $_dest_tmp,
            'url_path'      => $_path_pdf
        );
    }

    /**
     * Récupérer tout les constatations par date
     * @param \DateTime $_date
     * @param integer $_id_centre
     * @param integer $_id_verificateur
     * @return array
     */
    public function getAllConstatationByFilter($_date, $_id_centre, $_id_verificateur)
    {
        $_entity = EntityName::CT_CONST_AV_DED;
        $_dql    = "SELECT cad FROM $_entity cad
                    WHERE cad.cadCreated LIKE :date";

        if ($_id_centre != null)
            $_dql .= " AND cad.ctCentre = :id_centre";
        if ($_id_verificateur != null)
            $_dql .= " AND cad.ctVerificateur = :id_verificateur";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('date', $_date . '%');

        if ($_id_centre != null)
            $_query->setParameter('id_centre', $_id_centre);
        if ($_id_verificateur != null)
            $_query->setParameter('id_verificateur', $_id_verificateur);

        return $_query->getResult();
    }

    /**
     * Générer fiche de contrôle constatatation avant dédouanement
     * @param \DateTime $_date
     * @param integer $_id_centre
     * @param integer $_id_verificateur
     * @return array
     */
    public function generateFicheDeControle($_date, $_id_centre, $_id_verificateur)
    {
        // Récupérer manager
        $_constatation_manager = $this->_container->get(ServiceName::SRV_METIER_TYPE_CONST_AV_DED);

        $_constatations    = $this->getAllConstatationByFilter($_date, $_id_centre, $_id_verificateur);
        $_user_connected   = $this->_container->get('security.token_storage')->getToken()->getUser();
        $_centre           = $_user_connected->getCtCentre();
        $_code_centre      = $_centre->getCtrCode();
        $_nom_centre       = $_centre->getCtrNom();
        $_province         = $_centre->getCtProvince();
        $_code_province    = $_province->getPrvCode();
        $_nom_province     = $_province->getPrvNom();
        $_nbr_constatation = count($_constatations);

        $_pv_directory    = $this->_container->getParameter('reporting_template_directory');
        $_source_template = $_pv_directory . PathReportingName::TEMPLATE_CONSTATATION_FICHE_CONTROLE;

        $_phpWord  = new PhpWord();
        $_template = $_phpWord->loadTemplate($_source_template);
        $_today    = date('dmY');

        $_num_fdc          = $_today . '_FDCTRL_' . $_code_province . '_' . $_code_centre . '_CONSTATATION';
        $_filename         = $_num_fdc;
        $_path             = $_pv_directory . PathReportingName::GENERATE_CONSTATATION_FICHE_CONTROLE;
        $_dest_tmp         = $_path . $_filename . '.docx';
        $_file_without_ext = $_filename;

        $_url_scheme = $this->_container->get('request_stack')->getCurrentRequest()->server->get('HTTP_HOST');
        $_path_pdf   = 'http://' . $_url_scheme . '/reporting/' . PathReportingName::GENERATE_CONSTATATION_FICHE_CONTROLE . $_filename . '.pdf';

        // Formater la date anglais en français
        $_date_parsed = \DateTime::createFromFormat('Y-m-d', $_date)->format('d/m/Y');

        // Centre, province et date
        $_template->setValue('centre', htmlspecialchars($_nom_centre));
        $_template->setValue('province', $_nom_province);
        $_template->setValue('date', $_date_parsed);

        $_template->cloneRow('proprietaire', $_nbr_constatation);

        foreach ($_constatations as $_key => $_constatation) {
            ++$_key;

            $_proprietaire    = $_constatation->getCadProprietaireNom();
            $_adresse         = $_constatation->getCadProprietaireAdresse();
            $_const_caracs    = $_constatation->getCtConstAvDedCaracs();
            $_provenance      = $_constatation->getCadProvenance();
            $_immatriculation = $_constatation->getCadImmatriculation();

            if (isset($_const_caracs) && count($_const_caracs) > 1) {
                foreach ($_const_caracs as $_const_carac) {
                    $_marque          = $_const_carac->getCtMarque()->getMrqLibelle();
                    $_const_carc_type = $_const_carac->getctConstAvDedType();
                    $_const_carac_crp = $_constatation_manager->getCtTypeConstatationAvDedouanementByLibelle("corps");

                    if ($_const_carc_type->getId() == $_const_carac_crp->getId()) {
                        $_d_marque = $_marque;
                    }
                }
            }

            $_template->setValue('proprietaire#' . $_key, htmlspecialchars($_proprietaire));
            $_template->setValue('adresse#' . $_key, htmlspecialchars($_adresse));
            $_template->setValue('marque#' . $_key, $_d_marque);
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
}
