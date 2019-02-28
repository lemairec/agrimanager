<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
// ...



class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name');
        $builder->add('repository', ChoiceType::class, array(
            'choices'  => array(
                'autres' => 'autres',
                'banque' => 'banque',
                'banque_caj' => 'banque_caj',
                'factures' => 'factures',
                'analyse_sol' => 'analyse_sol',
            ),
        ));
        $builder->add('docFile', VichFileType::class, array(
            'required' => false,
            'allow_delete' => true,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
