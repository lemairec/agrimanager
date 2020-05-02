<?php

namespace App\Form\Gestion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EcritureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('compte', EntityType::class, array(
            'class'        => 'App:Gestion\Compte',
            'choice_label' => 'name',
            'choices' => $options['comptes'],
        ));
        $builder->add('campagne', EntityType::class, array(
            'class'        => 'App:Campagne',
            'choices' => $options['campagnes'],
        ));
        $builder->add('value');
        
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Gestion\Ecriture',
            'comptes' => null,
            'campagnes' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_ecriture';
    }


}
