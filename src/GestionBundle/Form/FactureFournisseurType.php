<?php

namespace GestionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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
            'comptes' => null
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
