<?php

namespace GestionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AppartementOperationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'html5' => false,
            'attr' => ['class' => 'js-datepicker'],
        ));
        $builder->add('type', ChoiceType::class, [
            'choices'  => [
                'Loyer' => 'Loyer',
                'Agence' => 'Agence',
                'Charges' => 'Charges',
                'Impot' => 'Impot',
                'Versement' => 'Versement',
                'Autre' => 'Autre'
            ]]);
        $builder->add('annee', ChoiceType::class, [
            'choices'  => [
                2018 => 2018,
                2017 => 2017
            ]]);
        $builder->add('value');
        $builder->add('save',      SubmitType::class, array('label'=> 'Valider'));

    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GestionBundle\Entity\AppartementOperation'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gestionbundle_appartementoperation';
    }


}
