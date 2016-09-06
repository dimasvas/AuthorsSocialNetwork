<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CompositionCategorySelectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', EntityType::class, array(
                'class' => 'AppBundle\Entity\CompositionCategory',
                'required'=>true,
                'label' => 'common.category',
                'placeholder' => 'form.placeholder.choose_an_option',
                'constraints' => array(
                    new NotBlank(),
                ),
            ))
            ->add('submit', SubmitType::class, array('label' => 'common.continue'))
            ->setMethod('POST');
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CompositionCategory'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_composition_category_select';
    }
}
