<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            'class'        => 'AppBundle:Ilot',
            'choices' => $options['ilots'],
        ));
        $builder->add('culture', EntityType::class, array(
            'class'        => 'AppBundle:Culture',
            'choices' => $options['cultures'],
        ));
        $builder->add('rendement');
        $builder->add('active');
        $builder->add('comment', TextareaType::class, array('required' => false));


        $builder->add('save',      SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Parcelle',
            'ilots' => null,
            'cultures' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'AppBundle_parcelle';
    }


}
