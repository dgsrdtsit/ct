<?php

namespace Ct\Service\MetierManagerBundle\Form;

use Ct\Service\MetierManagerBundle\Entity\CtCentre;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CtCarteGriseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cgImmatriculation', TextType::class, array(
                'label'    => "Numéro d'immatriculation",
                'required' => true
            ))
            ->add('cgNumIdentification', TextType::class, array(
                'label'    => "Numéro d'identification",
                'required' => true
            ))

            ->add('cgNom', TextType::class, array(
                'label'    => "Nom du propriétaire",
                'required' => true
            ))

//            ->add('cgPrenom', TextType::class, array(
//                'label'    => "Prénom du propriétaire",
//                'required' => true
//            ))

            ->add('cgProfession', TextType::class, array(
                'label'    => "Profession",
                'required' => true
            ))

            ->add('cgAdresse', TextType::class, array(
                'label'    => "Adresse",
                'required' => true
            ))

            ->add('cgPhone', TextType::class, array(
                'label'    => "Téléphone",
                'required' => true
            ))

            ->add('cgCommune', TextType::class, array(
                'label'    => "Commune",
                'required' => true
            ))

            ->add('cgNbrAssis', IntegerType::class, array(
                'label'    => "Places assises",
                'required' => true
            ))

            ->add('cgNbrDebout', IntegerType::class, array(
                'label'    => "Places débouts",
                'required' => false
            ))

            ->add('cgPuissanceAdmin', IntegerType::class, array(
                'label'    => "Puissance administrative",
                'required' => true
            ))

            ->add('cgPatente', TextType::class, array(
                'label'    => "Patente",
                'required' => false
            ))

            ->add('cgAni', TextType::class, array(
                'label'    => "ANI",
                'required' => false
            ))

            ->add('cgRta', TextType::class, array(
                'label'    => "RTA",
                'required' => false
            ))

            ->add('cgLieuCarteViolette', TextType::class, array(
                'label'    => "Lieu carte violette",
                'required' => false
            ))

            ->add('cgNumCarteViolette', TextType::class, array(
                'label'    => "N° carte violette",
                'required' => false
            ))

            ->add('cgNumVignette', TextType::class, array(
                'label'    => "N° licence",
                'required' => false
            ))

            ->add('cgLieuVignette', TextType::class, array(
                'label'    => "Lieu vignette",
                'required' => false
            ))

            ->add('cgIsTransport', CheckboxType::class, array(
                'label'    => "Transport",
                'required' => false
            ))

            ->add('cgNomCooperative', TextType::class, array(
                'label'    => "Nom coopérative",
                'required' => false
            ))

            ->add('cgItineraire', TextType::class, array(
                'label'    => "Itinéraire",
                'required' => false
            ))

            ->add('cgAni', TextType::class, array(
                'label'    => "ANI",
                'required' => false
            ))

            ->add('cgDateEmission', DateType::class, array(
                'label'    => "Date d'émission",
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'attr'     => array('class' => 'datepicker'),
                'required' => true
            ))

            ->add('cgMiseEnService', DateType::class, array(
                'label'    => "Date de première mise en circulation",
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'attr'     => array('class' => 'datepicker'),
                'required' => false
            ))

            ->add('cgDateCarteViolette', DateType::class, array(
                'label'    => "Date de carte violette",
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'attr'     => array('class' => 'datepicker'),
                'required' => false
            ))

            ->add('cgDateVignette', DateType::class, array(
                'label'    => "Date de vignette",
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'attr'     => array('class' => 'datepicker'),
                'required' => false
            ))

            ->add('ctCarosserie', EntityType::class, array(
                'label'         => 'Carrosserie',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtCarosserie',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('c')
                        ->orderBy('c.crsLibelle', 'ASC');
                },
                'choice_label'  => 'crsLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true
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

            ->add('ctSourceEnergie', EntityType::class, array(
                'label'         => "Source d'énergie",
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtSourceEnergie',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('s')
                        ->orderBy('s.sreLibelle', 'ASC');
                },
                'choice_label'  => 'sreLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true
            ))

            ->add('ctZoneDeserte', EntityType::class, array(
                'label'         => 'Zone de déserte',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtZoneDeserte',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('zd')
                        ->orderBy('zd.zdLibelle', 'ASC');
                },
                'choice_label'  => 'zdLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => false
            ))

            ->add('ctVehicule', CtVehiculeType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ct\Service\MetierManagerBundle\Entity\CtCarteGrise'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ct_service_metiermanagerbundle_carte_grise';
    }
}
