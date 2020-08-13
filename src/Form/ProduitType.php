<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ProduitType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')->add('type', ChoiceType::class, array(
            'choices'  =>
            ["autre" => "autre"
            , "agroequipements" => "agroequipements"
            , "engrais" => "engrais"
            , "engrais organique" => "engrais organique"
            , "oligos" => "oligos"
            , "semences" => "semences"
            , "phytos" => "phytos"
            , "services" => "services"
            , "lisa" => "lisa"]
                ));
        $builder->add('qty')->add('price')->add('unity')->add('ephyProduit');
        $builder->add('bio');
        $builder->add('n')->add('p')->add('k')->add('s')->add('mg');

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Produit'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_produit';
    }


}
