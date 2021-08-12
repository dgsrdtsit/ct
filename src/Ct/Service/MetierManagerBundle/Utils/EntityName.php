<?php

namespace Ct\Service\MetierManagerBundle\Utils;

/**
 * Class EntityName
 * Classe qui contient les noms constante de tout les entités
 * @package Eternaly\Service\MetierManagerBundle\Utils
 */
class EntityName
{
    const USER                  = 'UserBundle:User';
    const CT_CAROSSERIE         = 'MetierManagerBundle:CtCarosserie';
    const CT_GENRE              = 'MetierManagerBundle:CtGenre';
    const CT_GENRE_TARIF        = 'MetierManagerBundle:CtGenreTarif';
    const CT_ANOMALIE           = 'MetierManagerBundle:CtAnomalie';
    const CT_ROLE               = 'MetierManagerBundle:CtRole';
    const CT_MOTIF              = 'MetierManagerBundle:CtMotif';
    const CT_MOTIF_TARIF        = 'MetierManagerBundle:CtMotifTarif';
    const CT_MARQUE             = 'MetierManagerBundle:CtMarque';
    const CT_CENTRE             = 'MetierManagerBundle:CtCentre';
    const CT_PROVINCE           = 'MetierManagerBundle:CtProvince';
    const CT_ENERGIE            = 'MetierManagerBundle:CtSourceEnergie';
    const CT_USAGE              = 'MetierManagerBundle:CtUsage';
    const CT_USAGE_TARIF        = 'MetierManagerBundle:CtUsageTarif';
    const CT_TYPE_ANOMALIE      = 'MetierManagerBundle:CtAnomalieType';
    const CT_VISITE             = 'MetierManagerBundle:CtVisite';
    const CT_TYPE_RECEPTION     = 'MetierManagerBundle:CtTypeReception';
    const CT_RECEPTION          = 'MetierManagerBundle:CtReception';
    const CT_UTILISATION        = 'MetierManagerBundle:CtUtilisation';
    const CT_CARTE_GRISE        = 'MetierManagerBundle:CtCarteGrise';
    const CT_VEHICULE           = 'MetierManagerBundle:CtVehicule';
    const CT_TYPE_CONST_AV_DED  = 'MetierManagerBundle:CtConstAvDedType';
    const CT_CONST_AV_DED       = 'MetierManagerBundle:CtConstAvDed';
    const CT_CONST_AV_DED_CARAC = 'MetierManagerBundle:CtConstAvDedCarac';
    const CT_VISITE_EXTRA       = 'MetierManagerBundle:CtVisiteExtra';
    const CT_VISITE_EXTRA_TARIF = 'MetierManagerBundle:CtVisiteExtraTarif';
    const CT_TYPE_VISITE        = 'MetierManagerBundle:CtTypeVisite';
    const CT_PROCES_VERBAL      = 'MetierManagerBundle:CtProcesVerbal';
    const CT_DROIT_PTAC         = 'MetierManagerBundle:CtDroitPtac';
    const CT_HISTORIQUE         = 'MetierManagerBundle:CtHistorique';
    const CT_TYPE_USAGE         = 'MetierManagerBundle:CtTypeUsage';
    const CT_GENRE_CATEGORIE    = 'MetierManagerBundle:CtGenreCategorie';
    const CT_TYPE_DROIT_PTAC    = 'MetierManagerBundle:CtTypeDroitPtac';
    const CT_ZONE_DESERTE       = 'MetierManagerBundle:CtZoneDeserte';
    /* Gestion des imprimés techniques */
    const CT_IMPRIME_TECH       = 'MetierManagerBundle:CtImprimeTech';
    const CT_BORDEREAU          = 'MetierManagerBundle:CtBordereau';
    const CT_IMPRIME_TECH_USE   = 'MetierManagerBundle:CtImprimeTechUse';
}
