<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class InterventionProduitType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('qty');
        $builder->add('name');
        #$builder->add('produit', EntityType::class, array(
        #    'class'        => Produit::class,
        #    'choice_label' => 'name',
        #));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\InterventionProduit'
        ));
    }

}
