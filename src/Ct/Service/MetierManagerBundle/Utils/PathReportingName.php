<?php

namespace Ct\Service\MetierManagerBundle\Utils;

/**
 * Class PathReportingName
 * Classe qui contient les noms constante des répertoires reporting
 * @package Eternaly\Service\MetierManagerBundle\Utils
 */
class PathReportingName
{
    const TEMPLATE_VISITE_CARTE_VISITE = 'template/visite/carte_visite.docx';
    const GENERATE_VISITE_CARTE_VISITE = 'generated/visite/';

    const TEMPLATE_VISITE_APTE = 'template/visite/visite_apte.docx';
    const GENERATE_VISITE_APTE = 'generated/visite/';

    const TEMPLATE_VISITE_INAPTE = 'template/visite/pv_visite_inapte.docx';
    const GENERATE_VISITE_INAPTE = 'generated/visite/';

    const TEMPLATE_CONTRE_VISITE = 'template/visite/contre_visite.docx';
    const GENERATE_CONTRE_VISITE = 'generated/visite/';

    const TEMPLATE_VISITE_FEUILLE_CAISSE        = 'template/visite/feuille_de_caisse.docx';
    const TEMPLATE_VISITE_FEUILLE_CAISSE_OMAVET = 'template/visite/feuille_de_caisse_omavet.docx';
    const GENERATE_VISITE_FEUILLE_CAISSE        = 'generated/visite/';

    const TEMPLATE_VISITE_FICHE_VERIFICATEUR = 'template/visite/fiche-verificateur-jour.docx';
    const GENERATE_VISITE_FICHE_VERIFICATEUR = 'generated/visite/';

    const TEMPLATE_VISITE_FICHE_ANOMALIE = 'template/visite/liste_anomalies.docx';
    const GENERATE_VISITE_FICHE_ANOMALIE = 'generated/visite/';

    const TEMPLATE_VISITE_BILAN_ANNUEL = 'template/visite/bilan_annuel.docx';
    const GENERATE_VISITE_BILAN_ANNUEL = 'generated/visite/';

    const TEMPLATE_VISITE_BILAN_MENSUEL = 'template/visite/bilan_mensuel.docx';
    const GENERATE_VISITE_BILAN_MENSUEL = 'generated/visite/';

    const TEMPLATE_VISITE_BILAN_TRIMESTRIEL = 'template/visite/bilan_trimestriel.docx';
    const GENERATE_VISITE_BILAN_TRIMESTRIEL = 'generated/visite/';

    const TEMPLATE_RECEPTION_ISOLE = 'template/reception/pv/pv_reception.docx';
    const TEMPLATE_RECEPTION_TYPE = 'template/reception/pv/pv_reception_type.docx';
    const GENERATE_RECEPTION = 'generated/reception/pv/';

    const TEMPLATE_CONSTATATION = 'template/constatation/pv_de_constatation_avec_recu.docx';
    const TEMPLATE_FDC_CONSTATATION = 'template/constatation/feuille_de_caisse_cad.docx';
    const GENERATE_CONSTATATION = 'generated/constatation/';

    const TEMPLATE_DUPLICATA_ISOLE = 'template/duplicata/duplicata_pv_reception_isole.docx';
    const TEMPLATE_DUPLICATA_TYPE = 'template/duplicata/duplicata_pv_reception_type.docx';
    const GENERATE_DUPLICATA = 'generated/reception/duplicata/';

    const TEMPLATE_REC_FEUILLE_DE_CAISSE_TYPE = 'template/reception/feuille_de_caisse/feuille_de_caisse_reception_type.docx';
    const TEMPLATE_REC_FEUILLE_DE_CAISSE_ISOLE = 'template/reception/feuille_de_caisse/feuille_de_caisse_reception_isole.docx';
    const TEMPLATE_REC_FEUILLE_DE_CAISSE = 'template/reception/feuille_de_caisse/feuille_de_caisse_reception.docx';
    const GENERATE_REC_FEUILLE_DE_CAISSE = 'generated/reception/feuille_de_caisse/';

    const TEMPLATE_REC_FICHE_DE_CONTROLE_TYPE = 'template/reception/fiche_controle/fiche_controle_type.docx';
    const TEMPLATE_REC_FICHE_DE_CONTROLE_ISOLE = 'template/reception/fiche_controle/fiche_controle_isole.docx';
    const TEMPLATE_REC_FICHE_DE_CONTROLE = 'template/reception/fiche_controle/fiche_controle.docx';
    const GENERATE_REC_FICHE_DE_CONTROLE = 'generated/reception/fiche_controle/';

    const TEMPLATE_REC_TYPE_BILAN_ANNUEL = 'template/reception/statistique/stat_reception_technique_type_annuelle.docx';
    const GENERATE_REC_TYPE_BILAN_ANNUEL = 'generated/reception/statistique/';

    const TEMPLATE_REC_TYPE_BILAN_TRIM = 'template/reception/statistique/stat_reception_technique_type_trim.docx';
    const GENERATE_REC_TYPE_BILAN_TRIM = 'generated/reception/statistique/';

    const TEMPLATE_REC_TYPE_BILAN_SEMS = 'template/reception/statistique/stat_reception_technique_type_sems.docx';
    const GENERATE_REC_TYPE_BILAN_SEMS = 'generated/reception/statistique/';

    const TEMPLATE_REC_ISOLE_BILAN_ANNUEL = 'template/reception/statistique/stat_reception_technique_isole_annuelle.docx';
    const GENERATE_REC_ISOLE_BILAN_ANNUEL = 'generated/reception/statistique/';

    const TEMPLATE_REC_ISOLE_BILAN_TRIM = 'template/reception/statistique/stat_reception_technique_isole_trim.docx';
    const GENERATE_REC_ISOLE_BILAN_TRIM = 'generated/reception/statistique/';

    const TEMPLATE_REC_ISOLE_BILAN_SEMS = 'template/reception/statistique/stat_reception_technique_isole_sems.docx';
    const GENERATE_REC_ISOLE_BILAN_SEMS = 'generated/reception/statistique/';

    const TEMPLATE_REC_ALL_BILAN_MENSUEL = 'template/reception/statistique/stat_reception_technique_mensuelle.docx';
    const GENERATE_REC_ALL_BILAN_MENSUEL = 'generated/reception/statistique/';

    const TEMPLATE_REC_ALL_BILAN_ANNUEL = 'template/reception/statistique/stat_reception_technique_annuelle.docx';
    const GENERATE_REC_ALL_BILAN_ANNUEL = 'generated/reception/statistique/';

    const TEMPLATE_REC_ALL_BILAN_TRIM = 'template/reception/statistique/stat_reception_technique_trim.docx';
    const GENERATE_REC_ALL_BILAN_TRIM = 'generated/reception/statistique/';

    const TEMPLATE_REC_ALL_BILAN_SEMS = 'template/reception/statistique/stat_reception_technique_sems.docx';
    const GENERATE_REC_ALL_BILAN_SEMS = 'generated/reception/statistique/';

    const TEMPLATE_REC_BILAN = 'template/reception/statistique/stat_reception_technique.docx';
    const GENERATE_REC_BILAN = 'generated/reception/statistique/';

    const TEMPLATE_VISITE_BILAN = 'template/visite/statistique_visite_technique.docx';
    const GENERATE_VISITE_BILAN = 'generated/visite/';

    const TEMPLATE_CONSTATATION_BILAN = 'template/constatation/stat_cad.docx';
    const GENERATE_CONSTATATION_BILAN = 'generated/constatation/';

    const TEMPLATE_CONSTATATION_FICHE_CONTROLE = 'template/constatation/fiche_controle.docx';
    const GENERATE_CONSTATATION_FICHE_CONTROLE = 'generated/constatation/';

    const TEMPLATE_BORDEREAU = 'template/bordereau/bordereau_livraison.docx';
    const GENERATE_BORDEREAU = 'generated/bordereau/';

    const TEMPLATE_FEUILLE_IT_USED = 'template/bordereau/feuille_utilisation_it.docx';
    const GENERATE_FEUILLE_IT_USED = 'generated/bordereau/';

}
