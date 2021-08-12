<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtConstatationAvDedouanementCarac;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtConstatationAvDedouanementCarac
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
     * Récuperer le repository constatation avant dédouanement
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_CONST_AV_DED_CARAC);
    }

    /**
     * Récuperer toutes les constatations avant dédouanement
     * @return array
     */
    public function getAllCtConstatationAvDedouanementCarac()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer toutes les constatations avant dédouanement par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtConstatationAvDedouanementCaracByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer un type constatation avant dédouanement par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtConstatationAvDedouanementCaracById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Enregistrer une constatation avant dédouanement
     * @param Entity $_const_av_ded_carac
     * @param string $_action
     * @return boolean
     */
    public function saveCtConstatationAvDedouanementCarac($_const_av_ded_carac, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_const_av_ded_carac);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer uneconstatation avant dédouanement
     * @param Entity $_const_av_ded_carac
     * @return boolean
     */
    public function deleteCtConstatationAvDedouanementCarac($_const_av_ded_carac)
    {
        $this->_entity_manager->remove($_const_av_ded_carac);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple de constatation avant dédouanement
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtConstatationAvDedouanementCarac($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_const_av_ded_carac = $this->getCtConstatationAvDedouanementCaracById($_id);
                $this->deleteCtConstatationAvDedouanementCarac($_const_av_ded_carac);
            }
        }

        return true;
    }
}
