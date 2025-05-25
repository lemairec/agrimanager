<?php

namespace App\Form\Cotation;

use App\Entity\Cotation\CotationProduit;
use App\Entity\Cotation\PrixMoyen;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\Extension\Core\Type\DateType;

class PrixMoyenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('source')
            ->add('campagne')
            ->add('produit')
            
        ;
        $builder->add('accompte_price');

        $builder->add('c1_price');
        $builder->add('c1_date', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'html5' => false,
            'required' => false,
            'attr' => ['class' => 'js-datepicker'],
        ));
        $builder->add('c2_price');
        $builder->add('c2_date', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'html5' => false,
            'required' => false,
            'attr' => ['class' => 'js-datepicker'],
        ));
        $builder->add('c3_price');
        $builder->add('c3_date', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'html5' => false,
            'required' => false,
            'attr' => ['class' => 'js-datepicker'],
        ));
        $builder->add('c4_price');
        $builder->add('c4_date', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'html5' => false,
            'required' => false,
            'attr' => ['class' => 'js-datepicker'],
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrixMoyen::class,
        ]);
    }
}
