<?php

namespace App\Form\Lemca;

use App\Entity\Lemca\Panel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PanelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('no_fabricant')
            ->add('generation')
            ->add('comment')
            ->add('kit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Panel::class,
        ]);
    }
}
