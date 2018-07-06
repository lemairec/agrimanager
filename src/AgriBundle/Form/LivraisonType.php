<?php

namespace AgriBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            'class'        => 'AgriBundle:Parcelle',
            'choices' => $options['parcelles'],
        ));
        $builder->add('espece')->add('poid_total')->add('tare')->add('poid_norme');
        $builder->add('humidite')->add('ps')->add('proteine')->add('calibrage');
        $builder->add('save',      SubmitType::class, array('label'=> 'Valider'));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AgriBundle\Entity\Livraison',
            'parcelles' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'agribundle_livraison';
    }


}
