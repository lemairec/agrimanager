<?php

namespace App\Form\Iot;

use App\Entity\Iot\Moteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('label')
            ->add('description')
            ->add('ecart_temperature')
            ->add('balise')
            ->add('is_auto')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Moteur::class,
        ]);
    }
}
