<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtMotif;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtMotif
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
     * Récuperer le repository motif
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_MOTIF);
    }

    /**
     * Récuperer tout les motifs
     * @return array
     */
    public function getAllCtMotif()
    {
        return $this->getRepository()->findAll();
    }

    public function getCtMotifByLibelle($_libelle)
    {
        $_entity_m = EntityName::CT_MOTIF;


        $_dql    = "SELECT t
                    FROM $_entity_m t 
                    WHERE t.mtfLibelle LIKE ?1";
        $_query  = $this->_entity_manager->createQuery($_dql);
        $_query->setParameter(1, '%'.$_libelle.'%');
        $_ret = $_query->getResult();
        if (count($_ret) > 0 && is_array($_ret))
            return $_ret[0];
        return $_ret;
    }

    /**
     * Récuperer tout les motifs par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtMotifByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un motif par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtMotifById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un motif
     * @param Entity $_motif
     * @param string $_action
     * @return boolean
     */
    public function saveCtMotif($_motif, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_motif);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un motif
     * @param Entity $_motif
     * @return boolean
     */
    public function deleteCtMotif($_motif)
    {
        $this->_entity_manager->remove($_motif);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un motif
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtMotif($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_motif = $this->getCtMotifById($_id);
                $this->deleteCtMotif($_motif);
            }
        }

        return true;
    }
}
