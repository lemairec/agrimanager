<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

class CultureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('color', null, array(
            'attr' => array('class' => 'tinymce'),
        ));

        $builder->add('ppfObjRendement');
        $builder->add('ppfAzoteUnite');
        $builder->add('ppfMiseEnReserve');
        $builder->add('greleRdt');
        $builder->add('grelePrix');
        $builder->add('metaCulture');
        
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Culture'
        ));
    }
}
