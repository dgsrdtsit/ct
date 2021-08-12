<?php

namespace Ct\Service\MetierManagerBundle\Form;

use Ct\Service\MetierManagerBundle\Entity\CtConstAvDed;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CtConstatationAvDedouanementCaracType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cadPremiereCircule', TextType::class, array(
                'label'    => "Date premiere mise en circulation",
                'required' => false
            ))
            ->add('cadCylindre', TextType::class, array(
                'label'    => "Cylindree (cm3)",
                'required' => false
            ))
            ->add('cadPuissance', TextType::class, array(
                'label'    => "Puissance administrative",
                'attr'     => array('pattern' => "[0-9]+([,\.][0-9]+)?"),
                'required' => false
            ))
            ->add('cadPoidsVide', TextType::class, array(
                'label'    => "Poids à vide",
                'attr'     => array('pattern' => "[0-9]+([,\.][0-9]+)?"),
                'required' => false
            ))

            ->add('cadChargeUtile', TextType::class, array(
                'label'    => "Charge utile",
                'attr'     => array('pattern' => "[0-9]+([,\.][0-9]+)?"),
                'required' => false
            ))
            ->add('cadPoidsTotalCharge', TextType::class, array(
                'label'    => "Poids total en charge",
                'attr'     => array('pattern' => "[0-9]+([,\.][0-9]+)?", 'readonly' => 'readonly'),
                'disabled' => false,
                'required' => false
            ))
            ->add('cadPoidsMaxima', TextType::class, array(
                'label'    => "Poids maxima",
                'required' => false
            ))
            ->add('cadNbrAssis', TextType::class, array(
                'label'    => "Nombre places assises",
                'attr'     => array('pattern' => "[0-9]+([,\.][0-9]+)?"),
                'required' => false
            ))
            ->add('cadLargeur', TextType::class, array(
                'label'    => "Largeur",
                'attr'     => array('pattern' => "[0-9]+([,\.][0-9]+)?"),
                'required' => false
            ))
            ->add('cadHauteur', TextType::class, array(
                'label'    => "Hauteur",
                'attr'     => array('pattern' => "[0-9]+([,\.][0-9]+)?"),
                'required' => false
            ))
            ->add('cadLongueur', TextType::class, array(
                'label'    => "Longueur",
                'attr'     => array('pattern' => "[0-9]+([,\.][0-9]+)?"),
                'required' => false
            ))
            ->add('cadNumSerieType', TextType::class, array(
                'label'    => "N° dans la série du type",
                'required' => false
            ))
            ->add('cadNumMoteur', TextType::class, array(
                'label'    => "N° du moteur",
                'required' => false
            ))
            ->add('cadTypeCar', TextType::class, array(
                'label'    => "Type",
                'required' => false
            ))
            ->add('ctSourceEnergie', EntityType::class, array(
                'label'         => 'Source',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtSourceEnergie',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('s')
                        ->orderBy('s.sreLibelle', 'ASC');
                },
                'choice_label'  => 'sreLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => false,
                'placeholder'   => '- Séléctionner Energie -',
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
                'required'      => false,
                'placeholder'  => '- Séléctionner Carosserie -',
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
                'required'      => false,
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
                'required'      => false,
                'placeholder'   => '- Séléctionner Genre -',
            ))

            ->add('ctConstAvDedType', EntityType::class, array(
                'class'        => 'Ct\Service\MetierManagerBundle\Entity\CtConstAvDedType',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('c')
                        ->orderBy('c.cadTpLibelle', 'ASC');
                },
                'choice_label'  => 'cadTpLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => false,
                'placeholder'  => '- Séléctionner Type -',

            ))
            ->add('cadInputCaracType', TextType::class, array(
                'label'    => "Type caracteristique",
                'disabled' => true,
                'required' => false,
                'mapped' => false
            ))

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ct\Service\MetierManagerBundle\Entity\CtConstAvDedCarac'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ct_service_metiermanagerbundle_constatation_av_ded_carac';
    }
}
