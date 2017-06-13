<?php

namespace AgriBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ParcelleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('surface')->add('name')->add('culture');
        $builder->add('ilot', EntityType::class, array(
            'class'        => 'AgriBundle:Ilot',
            'choices' => $options['ilots'],
        ));
        $builder->add('active')->add('comment');


        $builder->add('save',      SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AgriBundle\Entity\Parcelle',
            'ilots' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'agribundle_parcelle';
    }


}
