<?php

namespace App\Form\Commercialisation;

use App\Entity\Commercialisation\Cotation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CotationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('source')
            ->add('campagne')
            ->add('produit')
            ->add('value')
            ->add('valueStockage')
            ->add('valueStockageEnd')
            ->add('date')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cotation::class,
        ]);
    }
}
