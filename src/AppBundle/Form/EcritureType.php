<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EcritureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('compte', EntityType::class, array(
            'class'        => 'AppBundle:Compte',
            'choice_label' => 'name',
            'choices' => $options['comptes'],
        ));
        $builder->add('campagne', EntityType::class, array(
            'class'        => 'AppBundle:Campagne',
            'choices' => $options['campagnes'],
        ));
        $builder->add('value');
        $builder->add('save',      SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Ecriture',
            'comptes' => null,
            'campagnes' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'AppBundle_ecriture';
    }


}
