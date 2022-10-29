<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class VarieteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('ordre');
        $builder->add('precedent', EntityType::class, array(
            'class'        => Culture::class,
            'choices' => $options['cultures'],
        ));
        $builder->add('surface');
        $builder->add('comment');
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'cultures' => null
        ));
    }
}
