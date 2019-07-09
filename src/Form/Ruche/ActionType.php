<?php

namespace App\Form\Ruche;

use App\Entity\Ruche\Action;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'html5' => false,
            'attr' => ['class' => 'js-datepicker'],
        ));

        $builder->add('type', ChoiceType::class, array(
            'choices' => array(
                'Observation' => 'Observation',
                'PoseHausse' => 'PoseHausse',
                'Recolte' => 'Recolte',
                'EnleverHausse' => 'EnleverHausse',
                'Enruchage' => 'Enruchage',
                'Mort' => 'Mort',
            )

        ));
        $builder->add('ruche', EntityType::class, array(
            'class'        => 'App:Ruche\Ruche',
            'choices' => $options['ruches'],
        ));
        $builder
            ->add('description');
        $builder->add('essaim', EntityType::class, array(
            'class'        => 'App:Ruche\Essaim',
            'choices' => $options['essaims'],
        ));
        $builder->add('rucher', EntityType::class, array(
            'class'        => 'App:Ruche\Rucher',
            'choices' => $options['ruchers'],
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Action::class,
            'essaims' => null,
            'ruches' => null,
            'ruchers' => null,
        ]);
    }
}
