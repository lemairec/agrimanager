<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Entity\Produit;
use App\Entity\Gestion\FactureFournisseur;

class AchatType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
            ));
            $builder->add('produit', EntityType::class, array(
                'class'        => Produit::class,
                'choices' => $options['produits'],
                'required' => false
            ));
            $builder->add('type', ChoiceType::class, array(
                'choices'  => array(
                    'achat' => 'achat',
                    'stock' => 'stock',
                    'complement' => 'complement',
                    'autre' => 'autre'
                ),
            ));
            $builder->add('facture', EntityType::class, array(
                'class'        => FactureFournisseur::class,
                'choices' => $options['factures'],
                'required' => false
            ));
            $builder->add('qty')->add('price_total')->add('externId')->add('comment');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Achat',
            'produits' => null,
            'factures' => null,
        ));
    }
}
