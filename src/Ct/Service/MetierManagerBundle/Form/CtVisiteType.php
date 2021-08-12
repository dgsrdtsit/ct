<?php

namespace Ct\Service\MetierManagerBundle\Form;

use Ct\Service\MetierManagerBundle\Entity\CtAnomalie;
use Ct\Service\MetierManagerBundle\Entity\CtCentre;
use Ct\Service\MetierManagerBundle\Entity\CtImprimeTechUse;
use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CtVisiteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->centre = $options['centre'];

        $builder
            ->add('vstDateExpiration', DateType::class, array(
                'label'    => "Date expiration",
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'attr'     => array('class' => 'datetimepicker'),
                'required' => true
            ))

            ->add('vstDureeReparation', TextType::class, array(
                'label'    => "Durée réparation accordée",
                'required' => false
            ))

            ->add('ctCentre', EntityType::class, array(
                'label'         => 'Centre Immatriculateur de',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtCentre',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('c')
                        ->orderBy('c.ctrNom', 'ASC');
                },
                'group_by'     => function (CtCentre $_centre) {
                    if ($_centre->getCtProvince())
                        return $_centre->getCtProvince()->getPrvNom();
                },
                'choice_label'  => 'ctrNom',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true
            ))

            ->add('ctUtilisation', EntityType::class, array(
                'label'         => 'Utilisation',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtUtilisation',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('u')
                        ->orderBy('u.utLibelle', 'DESC');
                },
                'choice_label'  => 'utLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true
            ))

            ->add('ctUsage', EntityType::class, array(
                'label'         => 'Usage',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtUsage',
//                'query_builder' => function (EntityRepository $_er) {
//                    return $_er
//                        ->createQueryBuilder('u')
//                        ->orderBy('u.usgLibelle', 'ASC');
//                },
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('u')
                        ->innerJoin('Ct\Service\MetierManagerBundle\Entity\CtUsageTarif', 'ut', 'WITH', 'ut.ctUsage = u.id')
                        ->where('ut.ctTypeVisite = 1') // visite sur site
                        ->orderBy('u.id', 'ASC');
                },
                'choice_label'  => 'usgLibelle',
                'placeholder'   => 'Choisir',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true
            ))

            ->add('ctTypeVisite', EntityType::class, array(
                'label'         => 'Type visite',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtTypeVisite',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('t')
                        ->orderBy('t.id', 'ASC');
                },
                'choice_label'  => 'tpvLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true
            ))

            ->add('ctVerificateur', EntityType::class, array(
                'label'         => 'Vérificateur',
                'class'         => 'Ct\Service\UserBundle\Entity\User',
                'query_builder' => function (EntityRepository $_er) {
                    $_query_builder = $_er
                        ->createQueryBuilder('u')
                        ->andWhere('u.ctRole = :id_role')
                        ->andWhere('u.usrPresence = 1')
                        ->setParameter('id_role', RoleName::ID_ROLE_VERIFICATEUR);

                    if ($this->centre != '') {
                        $_query_builder
                            ->andWhere('u.ctCentre = :id_centre')
                            ->setParameter('id_centre', $this->centre);
                    }

                    $_query_builder->orderBy('u.usrName', 'ASC');

                    return $_query_builder;
                },
                'choice_label'  => 'usrName',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true
            ))

            ->add('ctAnomalie', EntityType::class, array(
                'label'         => 'Anomalie(s)',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtAnomalie',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('a')
                        ->orderBy('a.anmlCode', 'ASC');
                },
                'group_by'     => function (CtAnomalie $_anomalie) {
                    if ($_anomalie->getCtAnomalieType())
                        return $_anomalie->getCtAnomalieType()->getAtpLibelle();
                },
                'choice_label'  => 'anmlCode',
                'multiple'      => true,
                'expanded'      => false,
                'required'      => false
            ))

            ->add('ctVisiteExtra', EntityType::class, array(
                'label'         => 'Vérifier imprimé(s) technique(s)',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtVisiteExtra',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('v')
                        ->orderBy('v.vsteLibelle', 'ASC');
                },
                'choice_label'  => 'vsteLibelle',
                'multiple'      => true,
                'expanded'      => false,
                'required'      => false
            ))

            ->add('ctCarteGrise', CtCarteGriseType::class)

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ct\Service\MetierManagerBundle\Entity\CtVisite',
            'centre'     => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ct_service_metiermanagerbundle_visite';
    }
}
