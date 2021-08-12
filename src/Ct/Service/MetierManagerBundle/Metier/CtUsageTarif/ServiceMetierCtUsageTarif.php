<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtUsageTarif;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Ct\Service\MetierManagerBundle\Entity\CtUsage;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Query\ResultSetMapping;

class ServiceMetierCtUsageTarif
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
     * Récuperer le repository usageTarif
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_USAGE_TARIF);
    }

    /**
     * Récuperer tout les usageTarifs
     * @return array
     */
    public function getAllCtUsageTarif()
    {
        return $this->getRepository()->findAll();
    }


    /**
     * Récuperer le tarif par usage
     * @return array
     */
    public function getAllCtUsageTarifByUsage()
    {

        $_entity = EntityName::CT_USAGE_TARIF;


        $_dql    = "SELECT u.usgLibelle as usgLibelle, u.usgValidite as usgValidite, t.id as id,
                    t.usgTrfPrix as usgTrfPrix, t.usgTrfAnnee as usgTrfAnnee, tv.tpvLibelle as tpvLibelle
                    FROM $_entity t 
                    LEFT JOIN t.ctUsage u
                    LEFT JOIN t.ctTypeVisite tv
                    ";


        //$_dql    = "SELECT g FROM $_entity g WHERE YEAR(g.usgTrfAnnee) = :annee AND g.ctUsage = :usage";

        $_query  = $this->_entity_manager->createQuery($_dql);
        return $_query->getResult();
    }

    /**
     * Récuperer le tarif par usage
     * @return array
     */
    public function getCtUsageTarifByUsageAndTypeVisite($_usage_id, $_type_visite)
    {

        $_entity = EntityName::CT_USAGE_TARIF;


        $_dql    = "SELECT u.usgLibelle as usgLibelle, u.usgValidite as usgValidite, t.id as id,
                    t.usgTrfPrix as usgTrfPrix, t.usgTrfAnnee as usgTrfAnnee, tv.tpvLibelle as tpvLibelle
                    FROM $_entity t 
                    LEFT JOIN t.ctUsage u
                    LEFT JOIN t.ctTypeVisite tv
                    WHERE u.id = :usage_id
                    AND tv.id = :type_visite
                    ";


        //$_dql    = "SELECT g FROM $_entity g WHERE YEAR(g.usgTrfAnnee) = :annee AND g.ctUsage = :usage";

        $_query  = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter('usage_id', $_usage_id);
        $_query->setParameter('type_visite', $_type_visite);
        return $_query->getSingleResult();
    }


    /**
     * Récuperer tout les usageTarifs par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtUsageTarifByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un usageTarif par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtUsageTarifById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un usageTarif
     * @param Entity $_usageTarif
     * @param string $_action
     * @return boolean
     */
    public function saveCtUsageTarif($_usageTarif, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_usageTarif);
        }
        try {
            $this->_entity_manager->flush();
        } catch (\Exception $e) {
            $errorMessage = "Cet usage a deja un tarif";
            $this->_container->get('session')->getFlashBag()->add('error', $errorMessage);
            return false;
        }

        return true;
    }

    /**
     * Supprimer un usageTarif
     * @param Entity $_usageTarif
     * @return boolean
     */
    public function deleteCtUsageTarif($_usageTarif)
    {
        $this->_entity_manager->remove($_usageTarif);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un usageTarif
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtUsageTarif($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_usageTarif = $this->getCtUsageTarifById($_id);
                $this->deleteCtUsageTarif($_usageTarif);
            }
        }

        return true;
    }
}
