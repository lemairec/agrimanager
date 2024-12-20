<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class GasoilType extends AbstractType
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
        $builder->add('litre');
        $builder->add('litrePompe');
        $builder->add('type', ChoiceType::class, [
            'choices'  => [
                'Cuve' => 'Cuve',
                'Steph' => 'Steph',
                'Pocquet' => 'Pocquet',
                'Papa' => 'Papa'
            ]]);

        $builder->add('materiel', EntityType::class, array(
            'class'        => Materiel::class,
            'choices' => $options['materiels'],
        ));
        $builder->add('nb_heure');
        $builder->add('comment');

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Gasoil',
            'materiels' => null
        ));
    }
}
