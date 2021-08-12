<?php

namespace Ct\Service\MetierManagerBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CtAnomalieType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('anmlLibelle', TextType::class, array(
                'label'    => "Libellé",
                'required' => true
            ))

            ->add('anmlCode', TextType::class, array(
                'label'    => "Code",
                'required' => true
            ))
            ->add('ctAnomalieType', EntityType::class, array(
                'label'         => 'Type Anomalie',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtAnomalieType',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('t')
                        ->orderBy('t.atpLibelle', 'ASC');
                },
                'choice_label'  => 'atpLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ct\Service\MetierManagerBundle\Entity\CtAnomalie'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ct_service_metiermanagerbundle_anomalie';
    }
}
