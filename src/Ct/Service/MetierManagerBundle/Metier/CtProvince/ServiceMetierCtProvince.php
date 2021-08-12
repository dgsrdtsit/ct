<?php

namespace Ct\Service\MetierManagerBundle\Metier\CtProvince;

use Doctrine\ORM\EntityManager;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\DependencyInjection\Container;

class ServiceMetierCtProvince
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
     * Récuperer le repository province
     * @return array
     */
    public function getRepository()
    {
        return $this->_entity_manager->getRepository(EntityName::CT_PROVINCE);
    }

    /**
     * Récuperer tout les provinces
     * @return array
     */
    public function getAllCtProvince()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Récuperer tout les anomalies par ordre
     * @param array $_order
     * @return array
     */
    public function getAllCtProvinceByOrder($_order)
    {
        return $this->getRepository()->findBy(array(), $_order);
    }

    /**
     * Récuperer une anomalie par identifiant
     * @param Integer $_id
     * @return array
     */
    public function getCtProvinceById($_id)
    {
        return $this->getRepository()->find($_id);
    }

    /**
     * Modifier un province
     * @param Entity $_province
     * @return boolean
     */
    public function updateCtProvince($_province)
    {
        $_province->setPrvUpdatedAt(new \DateTime());

        $this->saveCtProvince($_province, 'update');
    }

    /**
     * Enregistrer un province
     * @param Entity $_province
     * @param string $_action
     * @return boolean
     */
    public function saveCtProvince($_province, $_action)
    {
        if ($_action == 'new') {
            $this->_entity_manager->persist($_province);
        }
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Supprimer un province
     * @param Entity $_province
     * @return boolean
     */
    public function deleteCtProvince($_province)
    {
        $this->_entity_manager->remove($_province);
        $this->_entity_manager->flush();

        return true;
    }

    /**
     * Suppression multiple d'un province
     * @param array $_ids
     * @return boolean
     */
    public function deleteGroupCtProvince($_ids)
    {
        if (count($_ids)) {
            foreach ($_ids as $_id) {
                $_province = $this->getCtProvinceById($_id);
                $this->deleteCtProvince($_province);
            }
        }

        return true;
    }
}
