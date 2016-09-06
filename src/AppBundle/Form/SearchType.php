<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Description of SearchType
 *
 * @author dimas
 */
class SearchType extends AbstractType 
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('category', EntityType::class, array(
                    'class' => 'AppBundle:CompositionCategory',
                    'label' => 'common.category',
                    'placeholder' => 'common.select',
                    'required' => false,
                    'mapped'   => false, 
                            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
    }
}
