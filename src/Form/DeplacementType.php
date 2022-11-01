<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeplacementType extends AbstractType
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
        $builder->add('name');
        $builder->add('vehicule', ChoiceType::class, [
            'choices'  => [
                '308' => '308',
                'Patrole' => 'Patrole'
            ]]);

        $builder->add('km');
        $builder->add('comment');

    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Deplacement'
        ));
    }
}
