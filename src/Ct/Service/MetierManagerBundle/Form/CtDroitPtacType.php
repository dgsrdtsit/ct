<?php

namespace Ct\Service\MetierManagerBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CtDroitPtacType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dpPrixMin', TextType::class, array(
                'label'    => "Poids minimum (T)",
                'required' => false
            ))
            ->add('dpPrixMax', TextType::class, array(
                'label'    => "Poids maximum (T)",
                'required' => false
            ))
            ->add('dpDroit', TextType::class, array(
                'label'    => "Droit",
                'required' => true
            ))
            ->add('ctGenreCategorie', EntityType::class, array(
                'label'         => 'Categorie',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtGenreCategorie',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('c')
                        ->orderBy('c.gcLibelle', 'ASC');
                },
                'choice_label'  => 'gcLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true,
                'placeholder'   => '- Séléctionner Categorie -',
            ))
            ->add('ctTypeDroitPtac', EntityType::class, array(
                'label'         => 'Type',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtTypeDroitPtac',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('c')
                        ->orderBy('c.tpDpLibelle', 'DESC');
                },
                'choice_label'  => 'tpDpLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true,
                'placeholder'   => '- Séléctionner Type -',
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ct\Service\MetierManagerBundle\Entity\CtDroitPtac'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ct_service_metiermanagerbundle_droit_ptac';
    }
}
