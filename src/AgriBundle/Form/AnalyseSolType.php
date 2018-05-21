<?php

namespace AgriBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            'class'        => 'AgriBundle:Parcelle',
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
        $builder->add('save',      SubmitType::class);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AgriBundle\Entity\AnalyseSol',
            'parcelles' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'agribundle_analysesol';
    }


}
