<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class AnalyseSolType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('parcelle', EntityType::class, array(
            'class'        => 'App:Parcelle',
            'choice_label' => 'completeName',
            'choices' => $options['parcelles'],
        ));
        $builder
            ->add('date', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
            ));
        $builder->add('mo');
        $builder->add('ph');
        $builder->add('p');
        $builder->add('k');
        $builder->add('mg');
        $builder->add('docFile', VichFileType::class, array(
            'required' => false,
            'allow_delete' => true,
        ));


    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\AnalyseSol',
            'parcelles' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_analysesol';
    }


}
