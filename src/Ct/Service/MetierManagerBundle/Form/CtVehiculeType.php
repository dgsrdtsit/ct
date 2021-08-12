<?php

namespace Ct\Service\MetierManagerBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CtVehiculeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vhcType', TextType::class, array(
                'label'    => "Type Vehicule",
                'required' => false
            ))
            ->add('vhcCylindre', TextType::class, array(
                'label'    => "Cylindrée",
                'required' => false
            ))

            ->add('vhcPuissance', TextType::class, array(
                'label'    => "Puissance",
                'attr'     => array('pattern' => "[0-9]+([,\.][0-9]+)?"),
                'required' => false
            ))

            ->add('vhcPoidsVide', TextType::class, array(
                'label'    => "Poids à vide",
                'attr'     => array('pattern' => "[0-9]+([,\.][0-9]+)?"),
                'required' => true
            ))

            ->add('vhcChargeUtile', TextType::class, array(
                'label'    => "Charge utile",
                'attr'     => array('pattern' => "[0-9]+([,\.][0-9]+)?"),
                'required' => true
            ))

            ->add('vhcPoidsTotalCharge', TextType::class, array(
                'label'    => "Poids total autorisé en charge",
                'attr'     => array(
                    'pattern'  => "[0-9]+([,\.][0-9]+)?",
                    'readonly' => true
                ),
                'required' => false
            ))

            ->add('vhcHauteur', TextType::class, array(
                'label'    => "Hauteur",
                'attr'     => array('pattern' => "[0-9]+([,\.][0-9]+)?"),
                'required' => false
            ))

            ->add('vhcLongueur', TextType::class, array(
                'label'    => "Longueur",
                'required' => false
            ))

            ->add('vhcLargeur', NumberType::class, array(
                'label'    => "Largeur",
                'attr'     => array('pattern' => "[0-9]+([,\.][0-9]+)?"),
                'required' => false
            ))

            ->add('vhcNumSerie', TextType::class, array(
                'label'    => "N° dans la série du type",
                'required' => true
            ))

            ->add('vhcNumMoteur', TextType::class, array(
                'label'    => "N° du moteur",
                'required' => true
            ))

            ->add('vhcProvenance', TextType::class, array(
                'label'    => "Provenance",
                'required' => false
            ))

            ->add('ctMarque', EntityType::class, array(
                'label'         => 'Marque',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtMarque',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('m')
                        ->orderBy('m.mrqLibelle', 'ASC');
                },
                'choice_label'  => 'mrqLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true,
                'placeholder'   => '- Séléctionner Marque -',
            ))

            ->add('ctGenre', EntityType::class, array(
                'label'         => 'Genre',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtGenre',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('g')
                        ->orderBy('g.grLibelle', 'ASC');
                },
                'choice_label'  => 'grLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true,
                'placeholder'   => '- Séléctionner Genre -',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ct\Service\MetierManagerBundle\Entity\CtVehicule'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ct_service_metiermanagerbundle_vehicule';
    }
}
