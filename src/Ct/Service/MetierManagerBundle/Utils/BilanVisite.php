<?php

namespace Ct\Service\MetierManagerBundle\Utils;

/**
 * Class BilanVisite
 * Classe qui contient les objets bilan des visites technique
 * @package Eternaly\Service\MetierManagerBundle\Utils
 */
class BilanVisite
{
    public $_genres = array(
        "VP", "VP300", "VL", "TV", "AE", "TPP", "TBr", "CU", "Tbe", "TMTP",
        "TM3", "TM6", "TM7", "TM10", "TM16", "REM6", "REM7", "REM10", "REM16",
        "TRR7", "TRR10", "TRR16", "SREM7", "SREM10", "SREM16", "TA"
    );
    public $_payant           = array();
    public $_gratuit          = array();
    public $_apte             = array();
    public $_inapte           = array();
    public $_total_horizontal = array();
    public $_total_vertical   = array();

    /**
     * Constructor
     */
    public function __construct() {
    }
}
