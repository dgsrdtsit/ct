<?php

namespace Ct\Service\MetierManagerBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CtUsageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('usgLibelle', TextType::class, array(
                'label'    => "Libellé",
                'required' => true
            ))

            ->add('usgValidite', NumberType::class, array(
                'label'    => "Validité (en mois)",
                'required' => true
            ))

            ->add('ctTypeUsage', EntityType::class, array(
                'label'         => 'Type usage',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtTypeUsage',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('tpu')
                        ->orderBy('tpu.tpuLibelle', 'ASC');
                },
                'choice_label'  => 'tpuLibelle',
                'placeholder'   => 'Choisir',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => false
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ct\Service\MetierManagerBundle\Entity\CtUsage'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ct_service_metiermanagerbundle_usage';
    }
}
