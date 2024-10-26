<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


use App\Entity\Ilot;
use App\Entity\Culture;
use App\Entity\Parcelle;


class ParcelleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('surface')->add('name');
        $builder->add('ilot', EntityType::class, array(
            'class'        => Ilot::class,
            'choices' => $options['ilots'],
            'required' => false
        ));
        $builder->add('culture', EntityType::class, array(
            'class'        => Culture::class,
            'choices' => $options['cultures'],
        ));
        $builder->add('active');
        $builder->add('precedent');
        $builder->add('couvert');
        $builder->add('comment', TextareaType::class, array('required' => false));



    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Parcelle::class,
            'ilots' => null,
            'cultures' => null
        ));
    }

}
