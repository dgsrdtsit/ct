<?php

namespace Ct\Service\MetierManagerBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CtCentreType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ctrNom', TextType::class, array(
                'label'    => "Libellé",
                'required' => true
            ))

            ->add('ctrCode', TextType::class, array(
                'label'    => "Code",
                'required' => true
            ))

            ->add('ctProvince', EntityType::class, array(
                'label'         => 'Province',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtProvince',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('p')
                        ->orderBy('p.prvNom', 'ASC');
                },
                'choice_label'  => 'prvNom',
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
            'data_class' => 'Ct\Service\MetierManagerBundle\Entity\CtCentre'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ct_service_metiermanagerbundle_centre';
    }
}
