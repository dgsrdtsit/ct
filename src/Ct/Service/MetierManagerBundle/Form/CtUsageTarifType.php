<?php

namespace Ct\Service\MetierManagerBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class CtUsageTarifType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ->add('ctUsage', EntityType::class, array(
                'class'        => EntityName::CT_USAGE,
                'label'        => "Usage",
                'choice_label' => 'usgLibelle',

            ))

            ->add('usgTrfPrix', TextType::class, array(
                'label'    => "Prix",
                'required' => true
            ))

            /*->add('usgTrfAnnee', DateType::class, array(
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'attr'     => array('class' => 'datepicker'),
                'label'    => "Annee",
                'required' => true
            ))*/
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ct\Service\MetierManagerBundle\Entity\CtUsageTarif'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ct_service_metiermanagerbundle_usage_tarif';
    }
}
