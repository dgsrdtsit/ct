<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtMotifTarif;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Ct\Service\MetierManagerBundle\Entity\CtMotif;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Query\ResultSetMapping;

class ServiceMetierCtMotifTarif
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
     * Récuperer le repository motifTarif
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_MOTIF_TARIF);
    }

    /**
     * Récuperer tout les motifTarifs
     * @return array
     */
    public function getAllCtMotifTarif()
    {
        return $this->getRepository()->findAll();
    }


    /**
     * Récuperer le tarif par motif
     * @return array
     */
    public function getAllCtMotifTarifByMotif()
    {

        $_entity_tarif = EntityName::CT_MOTIF_TARIF;

        $_dql    = "SELECT m.mtfLibelle as mtfLibelle, t.id as id,
                    t.mtfTrfPrix as mtfTrfPrix, t.mtfTrfDate as mtfTrfDate
                    FROM $_entity_tarif t 
                    LEFT JOIN t.ctMotif m
                    ";
        $_query  = $this->_entity_manager->createQuery($_dql);
        return $_query->getResult();
    }

    public function getCtMotifTarifByMotif($_motif)
    {
        $_motif_tarif = $this->getRepository()->findBy(array('ctMotif' => $_motif->getId()));
        if ($_motif_tarif) {
            return $_motif_tarif[0]->getMtfTrfPrix();
        }
        return false;
    }


    /**
     * Récuperer tout les motifTarifs par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtMotifTarifByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un motifTarif par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtMotifTarifById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un motifTarif
     * @param Entity $_motifTarif
     * @param string $_action
     * @return boolean
     */
    public function saveCtMotifTarif($_motifTarif, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_motifTarif);
        }
        try {
            $this->_entity_manager->flush();
        } catch (\Exception $e) {
            $errorMessage = "Ce motif a deja un tarif";
            $this->_container->get('session')->getFlashBag()->add('error', $errorMessage);
            return false;
        }

        return true;
    }

    /**
     * Supprimer un motifTarif
     * @param Entity $_motifTarif
     * @return boolean
     */
    public function deleteCtMotifTarif($_motifTarif)
    {
        $this->_entity_manager->remove($_motifTarif);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un motifTarif
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtMotifTarif($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_motifTarif = $this->getCtMotifTarifById($_id);
                $this->deleteCtMotifTarif($_motifTarif);
            }
        }

        return true;
    }
}
