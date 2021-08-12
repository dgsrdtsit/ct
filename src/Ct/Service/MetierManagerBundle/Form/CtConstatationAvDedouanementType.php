<?php

namespace Ct\Service\MetierManagerBundle\Form;

use Ct\Service\MetierManagerBundle\Entity\CtConstAvDed;
use Ct\Service\MetierManagerBundle\Entity\CtConstAvDedCarac;
use Ct\Service\MetierManagerBundle\Utils\RoleName;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CtConstatationAvDedouanementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Ct\Service\UserBundle\Entity\User */
        $_user_connected = $options['user_connected'];
        $_ct_centre = $_user_connected->getCtCentre();

        $builder
            ->add('cadImmatriculation', TextType::class, array(
                'label'    => "Immatriculation",
                'required' => false
            ))

            ->add('cadObservation', TextType::class, array(
                'label'    => "Observation",
                'required' => false
            ))

            ->add('cadDateEmbarquement', DateType::class, array(
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'attr'     => array('class' => 'datepicker'),
                'label'    => "Date d'embarquement",
                'required' => false
            ))

            ->add('cadLieuEmbarquement', TextType::class, array(
                'label'    => "Lieu d'embarquement",
                'required' => false
            ))

            ->add('cadProvenance', TextType::class, array(
                'label'    => "Provenance",
                'required' => false
            ))
            ->add('cadDivers', TextType::class, array(
                'label'    => "Divers",
                'required' => false
            ))
            ->add('cadProprietaireNom', TextType::class, array(
                'label'    => "Propriétaire",
                'required' => false
            ))
            ->add('cadProprietaireAdresse', TextType::class, array(
                'label'    => "Adresse",
                'required' => false
            ))

            ->add('cadConforme', ChoiceType::class, array(
                'label'    => "Conforme",
                'choices' => array(
                    'Oui' => 1,
                    'Non' => 0,
                ),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ))
            ->add('cadBonEtat', ChoiceType::class, array(
                'label'    => "Bon état",
                'choices' => array(
                    'Oui' => 1,
                    'Non' => 0,
                ),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ))
            ->add('cadSecPers', ChoiceType::class, array(
                'label'    => "Sécurite des personnes",
                'choices' => array(
                    'Oui' => 1,
                    'Non' => 0,
                ),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ))
            ->add('cadSecMarch', ChoiceType::class, array(
                'label'    => "Sécurite des marchandises",
                'choices' => array(
                    'Oui' => 1,
                    'Non' => 0,
                ),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ))
            ->add('cadProtecEnv', ChoiceType::class, array(
                'label'    => "Protection de l'environnement",
                'choices' => array(
                    'Oui' => 1,
                    'Non' => 0,
                ),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ))
            ->add('ctVerificateur', EntityType::class, array(
                'label'         => 'Vérificateur',
                'class'         => 'Ct\Service\UserBundle\Entity\User',
                'query_builder' => function (EntityRepository $_er) use ($_ct_centre) {
                    return $_er
                        ->createQueryBuilder('u')
                        ->andWhere('u.ctRole = :id_role')
                        ->andWhere('u.ctCentre = :id_centre')
                        ->setParameter('id_role', RoleName::ID_ROLE_VERIFICATEUR)
                        ->setParameter('id_centre', $_ct_centre->getId())
                        ->orderBy('u.usrName', 'ASC');
                },
                'choice_label'  => 'usrName',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true
            ))
            ->add('ctConstAvDedCaracs', CollectionType::class, array(
                // each entry in the array will be an "ctconstavdedcarac" field
                'entry_type'   => CtConstatationAvDedouanementCaracType::class,
                'entry_options' => array('label' => false),
            ))

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CtConstAvDed::class
        ));
        $resolver->setRequired('user_connected');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ct_service_metiermanagerbundle_constatation_av_ded';
    }
}
