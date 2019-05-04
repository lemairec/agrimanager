<?php

namespace App\Form\Ruche;

use App\Entity\Ruche\Ruche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RucheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('color')
            ->add('nbrCadres')
            ->add('description')
            ->add('rucher')
            ->add('essaim')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ruche::class,
        ]);
    }
}
