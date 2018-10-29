<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ParcelleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('surface')->add('name');
        $builder->add('ilot', EntityType::class, array(
            'class'        => 'App:Ilot',
            'choices' => $options['ilots'],
        ));
        $builder->add('culture', EntityType::class, array(
            'class'        => 'App:Culture',
            'choices' => $options['cultures'],
        ));
        $builder->add('rendement');
        $builder->add('active');
        $builder->add('comment', TextareaType::class, array('required' => false));


        
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Parcelle',
            'ilots' => null,
            'cultures' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_parcelle';
    }


}
