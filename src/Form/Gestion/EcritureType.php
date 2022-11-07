<?php

namespace App\Form\Gestion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Gestion\Compte;
use App\Entity\Campagne;

class EcritureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('compte', EntityType::class, array(
            'class'        => Compte::class,
            'choice_label' => 'name',
            'choices' => $options['comptes'],
        ));
        $builder->add('campagne', EntityType::class, array(
            'class'        => Campagne::class,
            'choices' => $options['campagnes'],
            'required' => false,
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
}
