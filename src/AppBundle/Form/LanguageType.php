<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\Language;

/**
 * Description of LanguageType
 *
 * @author dimas
 */
class LanguageType extends AbstractType 
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('language', ChoiceType::class, array(
                    'label' => 'common.composition_language',
                    'required' => false,
                    'placeholder' => 'common.select',
                    'choices' => array_flip(Language::getCompositionLang())
                )
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
    }
}
