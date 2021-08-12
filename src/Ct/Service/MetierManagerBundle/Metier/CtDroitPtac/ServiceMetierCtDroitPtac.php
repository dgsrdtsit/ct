<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtDroitPtac;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtDroitPtac
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
     * Récuperer le repository droit_ptac
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_DROIT_PTAC);
    }

    /**
     * Récuperer tout les droit_ptacs
     * @return array
     */
    public function getAllCtDroitPtac()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les droit_ptacs par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtDroitPtacByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }


    public function getDroitByCategorieGenreEtPtac($_ctGenreCategorie, $_ptac, $_type)
    {
        $_entity_ptac = EntityName::CT_DROIT_PTAC;
        $_is_calculable = $_ctGenreCategorie->getGcIsCalculable();
        if ($_is_calculable) {
            $_dql    = "SELECT t.dpDroit
                    FROM $_entity_ptac t
                    LEFT JOIN t.ctGenreCategorie g
                    LEFT JOIN t.ctTypeDroitPtac tp
                    WHERE g.id = ?1 AND t.dpPrixMin <= ?2 AND ?2 < t.dpPrixMax
                    AND tp.tpDpLibelle LIKE ?3
                    ";

            $_query  = $this->_entity_manager->createQuery($_dql);
            $_query->setParameter(1, $_ctGenreCategorie->getId());
            $_query->setParameter(2, $_ptac);
            $_query->setParameter(3, '%'.$_type.'%');
        } else {
            $_dql    = "SELECT t.dpDroit
                    FROM $_entity_ptac t
                    LEFT JOIN t.ctGenreCategorie g
                    LEFT JOIN t.ctTypeDroitPtac tp
                    WHERE g.id = ?1
                    AND tp.tpDpLibelle LIKE ?2
                    ";

            $_query  = $this->_entity_manager->createQuery($_dql);
            $_query->setParameter(1, $_ctGenreCategorie->getId());
            $_query->setParameter(2, '%'.$_type.'%');
        }

        $_res = $_query->getResult();
        if (isset($_res) && count($_res) > 0) {
            return $_res[0]['dpDroit'];
        }
        return null;

    }

    /**
     * Récuperer un droit_ptac par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtDroitPtacById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un droit_ptac
     * @param Entity $_droit_ptac
     * @param string $_action
     * @return boolean
     */
    public function saveCtDroitPtac($_droit_ptac, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_droit_ptac);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un droit_ptac
     * @param Entity $_droit_ptac
     * @return boolean
     */
    public function deleteCtDroitPtac($_droit_ptac)
    {
        $this->_entity_manager->remove($_droit_ptac);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un droit_ptac
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtDroitPtac($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_droit_ptac = $this->getCtDroitPtacById($_id);
                $this->deleteCtDroitPtac($_droit_ptac);
            }
        }

        return true;
    }
}
