<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MaterielType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('dateAchat', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'html5' => false,
            'attr' => ['class' => 'js-datepicker'],
        ));
        $builder->add('annee')->add('caracteristique')
        ->add('comment', TextareaType::class, array('required' => false));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Materiel'
        ));
    }
}
