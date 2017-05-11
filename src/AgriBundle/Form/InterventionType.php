<?php

namespace AgriBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AgriBundle\Form\InterventionParcelleType;
use AgriBundle\Form\InterventionProduitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class InterventionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
            ));
        $builder->add('type');
        $builder->add('comment');
        /**$builder->add('parcelles', CollectionType::class, array(
            'entry_type' => InterventionParcelleType::class,
            'allow_add'    => true,
            'allow_delete' => true
        ));
        $builder->add('produits', CollectionType::class, array(
            'entry_type' => InterventionProduitType::class,
            'allow_add'    => true,
            'allow_delete' => true
        ));**/
        $builder->add('save',      SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AgriBundle\Entity\Intervention'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'agribundle_intervention';
    }


}
