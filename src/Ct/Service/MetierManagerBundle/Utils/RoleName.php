<?php


namespace Ct\Service\MetierManagerBundle\Utils;


/**
 * Class RoleName
 * Classe qui contient les noms constante des rôles utilisateur
 * @package Eternaly\Service\MetierManagerBundle\Utils
 */
class RoleName
{
    // Nom rôle
    const ROLE_SUPER_ADMINISTRATEUR = 'ROLE_SUPERADMIN';
    const ROLE_ADMINISTRATEUR       = 'ROLE_ADMIN';
    const ROLE_VERIFICATEUR         = 'ROLE_VERIFICATEUR';
    const ROLE_RECEPTION            = 'ROLE_RECEPTION';
    const ROLE_VISITE               = 'ROLE_VISITE';
    const ROLE_STAFF                = 'ROLE_STAFF';
    const ROLE_APPRO                = 'ROLE_APPRO';

    // Identifiant rôle
    const ID_ROLE_SUPERADMIN    = 1;
    const ID_ROLE_ADMIN         = 2;
    const ID_ROLE_VERIFICATEUR  = 3;
    const ID_ROLE_RECEPTION     = 4;
    const ID_ROLE_VISITE        = 5;
    const ID_ROLE_STAFF         = 6;
    const ID_ROLE_APPRO         = 7;
}
