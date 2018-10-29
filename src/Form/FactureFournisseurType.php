<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vich\UploaderBundle\Form\Type\VichFileType;

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
            'class'        => 'App:Campagne',
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
            'class'        => 'App:Compte',
            'choice_label' => 'name',
            'choices' => $options['banques'],
        ));
        $builder->add('compte', EntityType::class, array(
            'class'        => 'App:Compte',
            'choice_label' => 'name',
            'choices' => $options['comptes'],
        ));
        $builder->add('factureFile', VichFileType::class, array(
            'required' => false,
            'allow_delete' => true,
        ));

        
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\FactureFournisseur',
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
        return 'App_facturefournisseur';
    }


}
