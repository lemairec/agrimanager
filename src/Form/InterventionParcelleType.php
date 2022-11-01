<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class InterventionParcelleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('parcelle', EntityType::class, array(
            'class'        => Parcelle::class,
            'choice_label' => 'completeName',
            'choices' => $options['parcelles'],
        ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\InterventionParcelle',
            'parcelles' => null
        ));
    }
}
