<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtVisiteExtraTarif;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Ct\Service\MetierManagerBundle\Entity\CtVisiteExtra;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Query\ResultSetMapping;

class ServiceMetierCtVisiteExtraTarif
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
     * Récuperer le repository visite_extra_tarif
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_VISITE_EXTRA_TARIF);
    }

    /**
     * Récuperer tout les visite_extra_tarifs
     * @return array
     */
    public function getAllCtVisiteExtraTarif()
    {
        return $this->getRepository()->findAll();
    }


    /**
     * Récuperer le tarif par visite_extra_
     * @return array
     */
    public function getAllCtVisiteExtraTarifByVisiteExtra()
    {

        $_entity_tarif = EntityName::CT_VISITE_EXTRA_TARIF;

        $_dql    = "SELECT ve.vsteLibelle as vsteLibelle, t.id as id,
                    t.vetPrix as vetPrix, t.vetAnnee as vetAnnee
                    FROM $_entity_tarif t 
                    LEFT JOIN t.ctVisiteExtra ve
                    ";
        $_query  = $this->_entity_manager->createQuery($_dql);
        return $_query->getResult();
    }


    /**
     * Récuperer tout les visite_extra_tarifs par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtVisiteExtraTarifByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un visite_extra_tarif par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtVisiteExtraTarifById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un visite_extra_tarif
     * @param Entity $_visite_extra_tarif
     * @param string $_action
     * @return boolean
     */
    public function saveCtVisiteExtraTarif($_visite_extra_tarif, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_visite_extra_tarif);
        }
        try {
            $this->_entity_manager->flush();
        } catch (\Exception $e) {
            $errorMessage = "Ce visite extra a deja un tarif";
            $this->_container->get('session')->getFlashBag()->add('error', $errorMessage);
            return false;
        }

        return true;
    }

    /**
     * Supprimer un visite_extra_tarif
     * @param Entity $_visite_extra_tarif
     * @return boolean
     */
    public function deleteCtVisiteExtraTarif($_visite_extra_tarif)
    {
        $this->_entity_manager->remove($_visite_extra_tarif);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un visite_extra_tarif
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtVisiteExtraTarif($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_visite_extra_tarif = $this->getCtVisiteExtraTarifById($_id);
                $this->deleteCtVisiteExtraTarif($_visite_extra_tarif);
            }
        }

        return true;
    }
}
