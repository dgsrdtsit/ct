<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtHistorique;

use Ct\Service\MetierManagerBundle\Entity\CtCarteGrise;
use Ct\Service\MetierManagerBundle\Entity\CtConstAvDed;
use Ct\Service\MetierManagerBundle\Entity\CtHistorique;
use Ct\Service\MetierManagerBundle\Entity\CtReception;
use Ct\Service\MetierManagerBundle\Entity\CtVisite;
use Ct\Service\MetierManagerBundle\Utils\ServiceName;
use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtHistorique
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
     * Récuperer le repository Historique
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_HISTORIQUE);
    }

    /**
     * Récuperer tout les Historiques
     * @return array
     */
    public function getAllCtHistorique()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les Historiques par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtHistoriqueByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un Historique par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtHistoriqueById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un Historique
     * @param Entity $_historique
     * @param string $_action
     * @return boolean
     */
    public function saveCtHistorique($_historique, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_historique);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un Historique
     * @param Entity $_historique
     * @return boolean
     */
    public function deleteCtHistorique($_historique)
    {
        $this->_entity_manager->remove($_historique);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un Historique
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtHistorique($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_historique = $this->getCtHistoriqueById($_id);
                $this->deleteCtHistorique($_historique);
            }
        }

        return true;
    }

    /**
     * Récuperer les cartes grise par numéro de série
     * @param string $_numero_serie
     * @return array
     */
    public function getAllCarteVisiteByNumeroSerie($_numero_serie)
    {
        $_entity = EntityName::CT_CARTE_GRISE;
        $_dql = "SELECT cg FROM $_entity cg
                    LEFT JOIN cg.ctVehicule vhc
                    WHERE vhc.vhcNumSerie = :numero_serie OR cg.cgImmatriculation = :numero_serie";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('numero_serie', $_numero_serie);

        return $_query->getResult();
    }

    /**
     * Récuperer les visites par numéro de série
     * @param string $_numero_serie
     * @return array
     */
    public function getAllVisiteByNumeroSerie($_numero_serie)
    {
        $_entity = EntityName::CT_VISITE;
        $_dql = "SELECT vst FROM $_entity vst
                    LEFT JOIN vst.ctCarteGrise cg 
                    LEFT JOIN cg.ctVehicule vhc
                    WHERE (vhc.vhcNumSerie = :numero_serie OR cg.cgImmatriculation = :numero_serie)
                    AND vst.vstIsContreVisite = 0";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('numero_serie', $_numero_serie);

        return $_query->getResult();
    }

    /**
     * Récuperer les contre visites par numéro de série
     * @param string $_numero_serie
     * @return array
     */
    public function getAllContreVisiteByNumeroSerie($_numero_serie)
    {
        $_entity = EntityName::CT_VISITE;
        $_dql = "SELECT vst FROM $_entity vst
                    LEFT JOIN vst.ctCarteGrise cg 
                    LEFT JOIN cg.ctVehicule vhc
                    WHERE (vhc.vhcNumSerie = :numero_serie OR cg.cgImmatriculation = :numero_serie)
                    AND vst.vstIsContreVisite = 1";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('numero_serie', $_numero_serie);

        return $_query->getResult();
    }

    /**
     * Récuperer les réceptions par numéro de série
     * @param string $_numero_serie
     * @return array
     */
    public function getAllReceptionByNumeroSerie($_numero_serie)
    {
        $_entity = EntityName::CT_RECEPTION;
        $_dql = "SELECT rcp FROM $_entity rcp
                    LEFT JOIN rcp.ctVehicule vhc
                    WHERE vhc.vhcNumSerie = :numero_serie";

        $_query = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('numero_serie', $_numero_serie);

        return $_query->getResult();
    }

    /**
     * Log modification des entites (Visite, Carte-Grise, Reception, Constatation)
     * @param $_user
     * @param $_entity_class
     * @param $_date_time_modif
     */
    public function logActionUser($_user, $_entity_class, $_date_time_modif)
    {
        if ($_entity_class instanceof CtReception) {
            $_entity_type = "RECEPTION";
            $_numero = $_entity_class->getRcpNumPv();
        } elseif ($_entity_class instanceof CtVisite) {
            $_entity_type = "VISITE";
            $_numero = $_entity_class->getVstNumPv();
        } elseif ($_entity_class instanceof CtCarteGrise) {
            $_entity_type = "CARTE-GRISE";
            $_numero = $_entity_class->getCgImmatriculation();
        } elseif ($_entity_class instanceof CtConstAvDed) {
            $_entity_type = "CONSTATATION AVANT DEDOUANEMENT";
            $_numero = $_entity_class->getCadImmatriculation();
        } else {
            $_entity_type = "UNDEFINED";
            $_numero = "NULL";
        }
        $_entity_id = $_entity_class->getId();
        $_user_name = $_user->getUsrName();
        $_user_email = $_user->getEmail();
        $_user_centre = $_user->getCtCentre();
        $_province = $_user_centre->getCtProvince();
        $_libelle_centre = $_user_centre->getCtrNom();
        $_code_province = $_province->getPrvCode();
        $_log = "L'utilisateur " . $_user_name . " (" . $_user_email . ") du [Centre:" . strtoupper($_libelle_centre) .
            ",Province:" . $_code_province . "] a fait une modification sur " .
            $_entity_type . "[Numero:" . $_numero . "] le " . $_date_time_modif;

        $_historique = new CtHistorique();
        $_historique->setCtUser($_user);
        $_historique->setHstDescription($_log);
        $_historique->setHstType($_entity_type);
        $_historique->setCtCentre($_user_centre);
        $_historique->setHstDateCreate(new \DateTime());

        $this->_entity_manager->persist($_historique);
        $this->_entity_manager->flush();
    }

    /**
     * Liste notifications
     * @param $_centre_id
     * @return mixed
     */
    public function getNotificationByCentre($_centre_id)
    {
        if ($_centre_id > 0)
            return $this->getRepository()->findBy(array(
                'ctCentre' => $_centre_id,
                'hstIsView' => 0),
                array("hstDateCreate" => "DESC"));
        return $this->getRepository()->findBy(array(
            'hstIsView' => 0),
            array("hstDateCreate" => "DESC"));
    }

    public function getDetailNotification($_historique_id)
    {
        $_historique = $this->getCtHistoriqueById($_historique_id);
        $_historique->setHstIsView(1);
        $this->_entity_manager->persist($_historique);
        $this->_entity_manager->flush();
        $_user = $_historique->getCtUser();

        $_user_centre = $_user->getCtCentre();
        $_province = $_user_centre->getCtProvince();

        $_libelle_centre = $_user_centre->getCtrNom();
        $_code_province = $_province->getPrvCode();
        $_desc = $_historique->getHstDescription();

        $_type = $_historique->getHstType();
        $_date = $_historique->getHstDateCreate()->format('d/m/Y H:i:s');
        return array(
            'centre' => $_libelle_centre,
            'province' => $_code_province,
            'desc' => $_desc,
            'type' => $_type,
            'date' => $_date

        );
    }

    public function getDetailAllNotifications()
    {
        $_historiques = $this->getRepository()
            ->findBy(array(
                'hstIsView' => 0),
                array(
                    "hstDateCreate" => "DESC"
                )
            );
        $_details_notif = array();
        foreach ($_historiques as $_historique) {
            $_historique->setHstIsView(1);
            $this->_entity_manager->persist($_historique);
            $this->_entity_manager->flush();
            $_user = $_historique->getCtUser();

            $_user_centre = $_user->getCtCentre();
            $_province = $_user_centre->getCtProvince();

            $_libelle_centre = $_user_centre->getCtrNom();
            $_code_province = $_province->getPrvCode();
            $_desc = $_historique->getHstDescription();

            $_type = $_historique->getHstType();
            $_date = $_historique->getHstDateCreate()->format('d/m/Y H:i:s');
            $_details_notif[] = array(
                'centre' => $_libelle_centre,
                'province' => $_code_province,
                'desc' => $_desc,
                'type' => $_type,
                'date' => $_date

            );
        }

        return $_details_notif;
    }

}
