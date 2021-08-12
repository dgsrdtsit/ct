<?php

namespace Ct\Service\MetierManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class CtVisiteExtraTarifType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ctVisiteExtra', EntityType::class, array(
                'class'        => EntityName::CT_VISITE_EXTRA,
                'label'        => "Visite Extra",
                'choice_label' => 'vsteLibelle',

            ))

            ->add('vetPrix', TextType::class, array(
                'label'    => "Prix",
                'required' => true
            ))

            /*->add('mtfTrfDate', DateType::class, array(
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
            'data_class' => 'Ct\Service\MetierManagerBundle\Entity\CtVisiteExtraTarif'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ct_service_metiermanagerbundle_visite_extra_tarif';
    }
}
