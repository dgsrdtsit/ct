<?php

namespace Ct\Service\MetierManagerBundle\Form;

use Ct\Service\MetierManagerBundle\Entity\CtCentre;
use Ct\Service\MetierManagerBundle\Entity\CtMotif;
use Ct\Service\MetierManagerBundle\Utils\EntityName;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CtReceptionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ctVehicule', CtVehiculeType::class)

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
                'required'      => true,
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
                'required'      => true,
                'placeholder'  => '- Séléctionner Carosserie -',
            ))

            ->add('rcpNbrAssis', TextType::class, array(
                'label'    => "Nombre place assis",
                'attr'     => array('pattern' => "[0-9]+"),
                'required' => true
            ))

            ->add('rcpNbrDebout', TextType::class, array(
                'label'    => "Nombre place debout",
                'attr'     => array('pattern' => "[0-9]+"),
                'required' => true
            ))

            ->add('rcpMiseService', DateType::class, array(
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'attr'     => array('class' => 'datepicker'),
                'label'    => "Année de première mise en circulation",
                'required' => false
            ))

            ->add('rcpImmatriculation', TextType::class, array(
                'label'    => "Num Immatriculation",
                'required' => false
            ))

            ->add('ctMotif', EntityType::class, array(
                'label'         => 'Motif',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtMotif',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('m')
                        //->where('m.mtfLibelle NOT LIKE :motif')
                        //->setParameter('motif', '%VNI%')
                        ->orderBy('m.mtfLibelle', 'ASC');
                },
                'choice_label'  => 'mtfLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true,
                'placeholder'   => '- Séléctionner Motif -',
//                'choice_attr' => function($key, $val, $index) {
//                    $disabled = false;
//                    if (stripos($key->getMtfLibelle(), "VNI") !== false) {
//                        $disabled = true;
//
//                    }
//                    return $disabled ? ['disabled' => 'disabled'] : [];
//                },
            ))

            ->add('rcpProprietaire', TextType::class, array(
                'label'    => "Proprietaire",
                'required' => true
            ))

            ->add('rcpAdresse', TextType::class, array(
                'label'    => "Adresse",
                'required' => true
            ))

            ->add('rcpProfession', TextType::class, array(
                'label'    => "Profession",
                'required' => true
            ))

            ->add('ctUtilisation', EntityType::class, array(
                'label'         => 'Utilisateur',
                'class'         => 'Ct\Service\MetierManagerBundle\Entity\CtUtilisation',
                'query_builder' => function (EntityRepository $_er) {
                    return $_er
                        ->createQueryBuilder('u')
                        ->orderBy('u.utLibelle', 'ASC');
                },
                'choice_label'  => 'utLibelle',
                'multiple'      => false,
                'expanded'      => false,
                'required'      => true,
                'placeholder'   => '- Séléctionner Utilisation -',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ct\Service\MetierManagerBundle\Entity\CtReception'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ct_service_metiermanagerbundle_reception';
    }
}
