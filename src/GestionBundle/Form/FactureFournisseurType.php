<?php

namespace GestionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FactureFournisseurType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('date', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'html5' => false,
            'attr' => ['class' => 'js-datepicker'],
        ));
        $builder->add('campagne', EntityType::class, array(
            'class'        => 'AppBundle:Campagne',
            'choices' => $options['campagnes'],
        ));
        $builder->add('type', ChoiceType::class, array(
            'choices' => array(
                'Achat' => 'Achat',
                'Vente' => 'Vente',
            )

        ));
        $builder->add('montantHT')->add('montantTTC')->add('compte');
        $builder->add('banque', EntityType::class, array(
            'class'        => 'GestionBundle:Compte',
            'choice_label' => 'name',
            'choices' => $options['banques'],
        ));
        $builder->add('compte', EntityType::class, array(
            'class'        => 'GestionBundle:Compte',
            'choice_label' => 'name',
            'choices' => $options['comptes'],
        ));
        $builder->add('brochure', FileType::class, array(
                    'label' => 'Votre photo : ',
                    'required' => false,
                    'data_class' => null
                    ));

        $builder->add('save',      SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GestionBundle\Entity\FactureFournisseur',
            'banques' => null,
            'comptes' => null,
            'campagnes' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gestionbundle_facturefournisseur';
    }


}
