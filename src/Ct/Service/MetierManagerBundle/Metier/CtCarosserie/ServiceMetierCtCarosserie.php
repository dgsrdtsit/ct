<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtCarosserie;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtCarosserie
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
     * Récuperer le repository carosserie
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_CAROSSERIE);
    }

    /**
     * Récuperer tout les carosseries
     * @return array
     */
    public function getAllCtCarosserie()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les carosseries par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtCarosserieByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un carosserie par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtCarosserieById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer un carosserie
     * @param Entity $_carosserie
     * @param string $_action
     * @return boolean
     */
    public function saveCtCarosserie($_carosserie, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_carosserie);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un carosserie
     * @param Entity $_carosserie
     * @return boolean
     */
    public function deleteCtCarosserie($_carosserie)
    {
        $this->_entity_manager->remove($_carosserie);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un carosserie
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtCarosserie($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_carosserie = $this->getCtCarosserieById($_id);
                $this->deleteCtCarosserie($_carosserie);
            }
        }

        return true;
    }
}
