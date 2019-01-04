<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CommercialisationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'html5' => false,
            'attr' => ['class' => 'js-datepicker'],
        ));
        $builder->add('culture', EntityType::class, array(
            'class'        => 'App:Culture',
            'choices' => $options['cultures'],
        ));
        $builder->add('type', ChoiceType::class, array(
            'choices'  => array(
                'vente' => 'vente',
                'complement' => 'complement',
                'prix_moyen' => 'prix_moyen',
            ),
        ));
        $builder->add('qty')->add('price_total')->add('comment');

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Commercialisation',
            'cultures' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_compte';
    }


}
