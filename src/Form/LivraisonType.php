<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LivraisonType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy HH:mm',
            'html5' => false,
            'attr' => ['class' => 'js-datepicker'],
        ));
        $builder->add('parcelle', EntityType::class, array(
            'class'        => 'App:Parcelle',
            'choices' => $options['parcelles'],
        ));
        $builder->add('espece')->add('poid_total')->add('tare')->add('poid_norme');
        $builder->add('humidite')->add('impurete')->add('ps')->add('proteine')->add('calibrage');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Livraison',
            'parcelles' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_livraison';
    }


}
